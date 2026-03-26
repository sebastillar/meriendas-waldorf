<?php

namespace App\Domain\Services;

use App\Domain\Models\Alumno;
use App\Domain\Models\Asignacion;
use App\Domain\Models\ConfiguracionCalendario;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use App\Domain\Repositories\CerealPorDiaRepositoryInterface;
use App\Domain\Repositories\DiaSinClaseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneradorAgendaService
{
    public function __construct(
        private AlumnoRepositoryInterface $alumnoRepository,
        private AsignacionRepositoryInterface $asignacionRepository,
        private CerealPorDiaRepositoryInterface $cerealPorDiaRepository,
        private DiaSinClaseRepositoryInterface $diaSinClaseRepository
    ) {}

    /**
     * Genera asignaciones para un mes. Elimina asignaciones futuras desde el primer día y regenera.
     */
    public function generarParaMes(int $anio, int $mes): int
    {
        $desdeMes = Carbon::createFromDate($anio, $mes, 1)->startOfDay();
        $config = ConfiguracionCalendario::where('anio', $anio)->first();
        if ($config && $config->fecha_inicio_clases) {
            $inicioClasesCarbon = $config->fecha_inicio_clases->copy()->startOfDay();
            $desde = $inicioClasesCarbon->greaterThan($desdeMes) ? $inicioClasesCarbon : $desdeMes;
        } else {
            $desde = $desdeMes;
        }
        $hastaMes = $desdeMes->copy()->endOfMonth();
        if ($config && $config->fecha_fin_clases) {
            $finClasesCarbon = $config->fecha_fin_clases->copy()->startOfDay();
            $hasta = $finClasesCarbon->lessThan($hastaMes) ? $finClasesCarbon : $hastaMes;
        } else {
            $hasta = $hastaMes;
        }
        $diasLectivos = $this->obtenerDiasLectivos($desde, $hasta);
        if ($diasLectivos->isEmpty()) {
            return 0;
        }

        $this->asignacionRepository->eliminarFuturasDesde($desde);
        $alumnosActivos = $this->alumnoRepository->activos();
        if ($alumnosActivos->count() < 2) {
            return 0;
        }

        $desdeRegeneracion = $desde->copy();
        $conteosSemana = [];
        $generadas = 0;
        /** @var array<string, Asignacion> Asignaciones ya guardadas en esta corrida (misma transacción anidada), por si el driver no ve aún el INSERT al leer el día lectivo siguiente. */
        $asignacionesPorFechaYmd = [];
        // Equidad solo dentro del mes que se genera: no usar conteos históricos de meses anteriores.
        $conteosHasta = [];
        foreach ($alumnosActivos as $alumno) {
            $conteosHasta[$alumno->id] = [
                'fruta' => 0,
                'elaboracion' => 0,
                'ultima_fruta' => null,
                'ultima_elaboracion' => null,
            ];
        }

        DB::transaction(function () use ($diasLectivos, $alumnosActivos, $desdeRegeneracion, &$conteosSemana, &$conteosHasta, &$generadas, &$asignacionesPorFechaYmd) {
            foreach ($diasLectivos as $fecha) {
                $weekKey = $this->claveSemanaLunes($fecha);
                if (! array_key_exists($weekKey, $conteosSemana)) {
                    $precarga = $this->asignacionRepository->getConteosPorRolEnSemanaAntesDe($fecha, $desdeRegeneracion);
                    $conteosSemana[$weekKey] = [
                        'fruta' => $precarga['fruta'],
                        'elaboracion' => $precarga['elaboracion'],
                    ];
                }

                // 1=lunes..7=domingo (ISO); nuestra tabla usa 1..5 para lun-vie
                $diaIso = $fecha->dayOfWeek === 0 ? 7 : $fecha->dayOfWeek;
                $cerealModel = $this->cerealPorDiaRepository->getPorDiaSemana($diaIso);
                $cereal = $cerealModel?->cereal ?? 'Sin cereal';

                $diaPrevioLectivo = $this->diaLectivoAnterior($fecha);
                $asignacionDiaPrevio = null;
                if ($diaPrevioLectivo) {
                    $ymdPrevio = $diaPrevioLectivo->toDateString();
                    $asignacionDiaPrevio = $asignacionesPorFechaYmd[$ymdPrevio]
                        ?? $this->asignacionRepository->getPorFecha($diaPrevioLectivo);
                }
                $excluirConsecFruta = [];
                $excluirConsecElaboracion = [];
                if ($asignacionDiaPrevio) {
                    $excluirConsecFruta[] = $asignacionDiaPrevio->alumno_elaboracion_id;
                    $excluirConsecElaboracion[] = $asignacionDiaPrevio->alumno_fruta_id;
                }

                $alumnoFruta = $this->elegirAlumnoParaRol(
                    $alumnosActivos,
                    $conteosHasta,
                    $conteosSemana,
                    $weekKey,
                    'fruta',
                    $fecha,
                    null,
                    $excluirConsecFruta,
                    true
                );
                if ($alumnoFruta === null) {
                    Log::warning('Generador meriendas: sin candidato para fruta con regla día previo; se relaja consecutividad.', [
                        'fecha' => $fecha->toDateString(),
                    ]);
                    $alumnoFruta = $this->elegirAlumnoParaRol(
                        $alumnosActivos,
                        $conteosHasta,
                        $conteosSemana,
                        $weekKey,
                        'fruta',
                        $fecha,
                        null,
                        [],
                        false
                    );
                }

                $alumnoElab = $this->elegirAlumnoParaRol(
                    $alumnosActivos,
                    $conteosHasta,
                    $conteosSemana,
                    $weekKey,
                    'elaboracion',
                    $fecha,
                    $alumnoFruta?->id,
                    $excluirConsecElaboracion,
                    true
                );
                if ($alumnoElab === null) {
                    Log::warning('Generador meriendas: sin candidato para elaboración con regla día previo; se relaja consecutividad.', [
                        'fecha' => $fecha->toDateString(),
                    ]);
                    $alumnoElab = $this->elegirAlumnoParaRol(
                        $alumnosActivos,
                        $conteosHasta,
                        $conteosSemana,
                        $weekKey,
                        'elaboracion',
                        $fecha,
                        $alumnoFruta?->id,
                        [],
                        false
                    );
                }

                if ($alumnoFruta && $alumnoElab) {
                    $asignacion = new Asignacion([
                        'fecha' => $fecha,
                        'alumno_fruta_id' => $alumnoFruta->id,
                        'alumno_elaboracion_id' => $alumnoElab->id,
                        'cereal' => $cereal,
                        'estado' => 'planificada',
                    ]);
                    $this->asignacionRepository->guardar($asignacion);
                    $asignacionesPorFechaYmd[$fecha->toDateString()] = $asignacion;
                    $generadas++;

                    $conteosSemana[$weekKey]['fruta'][$alumnoFruta->id] = ($conteosSemana[$weekKey]['fruta'][$alumnoFruta->id] ?? 0) + 1;
                    $conteosSemana[$weekKey]['elaboracion'][$alumnoElab->id] = ($conteosSemana[$weekKey]['elaboracion'][$alumnoElab->id] ?? 0) + 1;

                    $conteosHasta[$alumnoFruta->id] = [
                        'fruta' => ($conteosHasta[$alumnoFruta->id]['fruta'] ?? 0) + 1,
                        'elaboracion' => $conteosHasta[$alumnoFruta->id]['elaboracion'] ?? 0,
                        'ultima_fruta' => $fecha->toDateString(),
                        'ultima_elaboracion' => $conteosHasta[$alumnoFruta->id]['ultima_elaboracion'] ?? null,
                    ];
                    $conteosHasta[$alumnoElab->id] = [
                        'fruta' => $conteosHasta[$alumnoElab->id]['fruta'] ?? 0,
                        'elaboracion' => ($conteosHasta[$alumnoElab->id]['elaboracion'] ?? 0) + 1,
                        'ultima_fruta' => $conteosHasta[$alumnoElab->id]['ultima_fruta'] ?? null,
                        'ultima_elaboracion' => $fecha->toDateString(),
                    ];
                }
            }
        });

        return $generadas;
    }

    /**
     * Clave estable de la semana (lunes de esa semana en formato Y-m-d).
     */
    private function claveSemanaLunes(Carbon $fecha): string
    {
        return $fecha->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
    }

    /**
     * @return Collection<int, Carbon>
     */
    private function obtenerDiasLectivos(Carbon $desde, Carbon $hasta): Collection
    {
        $fechas = collect();
        $cursor = $desde->copy();
        while ($cursor->lte($hasta)) {
            if ($cursor->isWeekday() && ! $this->diaSinClaseRepository->esDiaSinClase($cursor)) {
                $fechas->push($cursor->copy());
            }
            $cursor->addDay();
        }

        return $fechas;
    }

    /**
     * Último día lectivo estrictamente anterior a {@see $fecha} (laborable y no “sin clase”).
     */
    private function diaLectivoAnterior(Carbon $fecha): ?Carbon
    {
        $cursor = $fecha->copy()->subDay()->startOfDay();
        for ($i = 0; $i < 800; $i++) {
            if ($cursor->isWeekday() && ! $this->diaSinClaseRepository->esDiaSinClase($cursor)) {
                return $cursor;
            }
            $cursor->subDay();
        }

        return null;
    }

    /**
     * @param  array<int, int>  $excluirPorConsecutividad  IDs a excluir por regla día lectivo previo (elaboración→fruta / fruta→elaboración)
     * @param  array<string, array{fruta: array<int, int>, elaboracion: array<int, int>}>  $conteosSemana
     */
    private function elegirAlumnoParaRol(
        Collection $alumnos,
        array $conteosHasta,
        array $conteosSemana,
        string $weekKey,
        string $rol,
        Carbon $fecha,
        ?int $excluirMismoDiaOtroRolId,
        array $excluirPorConsecutividad = [],
        bool $aplicarReglaConsecutivos = true
    ): ?Alumno {
        $keyCount = $rol === 'fruta' ? 'fruta' : 'elaboracion';
        $keyLast = $rol === 'fruta' ? 'ultima_fruta' : 'ultima_elaboracion';

        $excluirIds = [];
        if ($excluirMismoDiaOtroRolId !== null) {
            $excluirIds[$excluirMismoDiaOtroRolId] = true;
        }
        if ($aplicarReglaConsecutivos) {
            foreach ($excluirPorConsecutividad as $id) {
                $excluirIds[(int) $id] = true;
            }
        }

        $candidatos = $alumnos->filter(fn ($a) => ! isset($excluirIds[$a->id]))->values();
        if ($candidatos->isEmpty()) {
            return null;
        }

        $semFruta = $conteosSemana[$weekKey]['fruta'] ?? [];
        $semElab = $conteosSemana[$weekKey]['elaboracion'] ?? [];

        $conConteos = $candidatos->map(function ($a) use ($conteosHasta, $keyCount, $keyLast, $semFruta, $semElab, $rol) {
            $c = $conteosHasta[$a->id] ?? ['fruta' => 0, 'elaboracion' => 0, 'ultima_fruta' => null, 'ultima_elaboracion' => null];
            $weekly = $rol === 'fruta'
                ? (int) ($semFruta[$a->id] ?? 0)
                : (int) ($semElab[$a->id] ?? 0);
            $nf = (int) ($c['fruta'] ?? 0);
            $ne = (int) ($c['elaboracion'] ?? 0);
            // Equilibrio entre roles (se aplica ANTES del conteo del rol, no después):
            // fruta → priorizar quien tiene más elaboraciones que frutas (ne - nf).
            // elaboración → priorizar quien tiene más frutas que elaboraciones (nf - ne).
            $balanceCruzado = $rol === 'fruta' ? ($ne - $nf) : ($nf - $ne);

            return [
                'alumno' => $a,
                'weekly' => $weekly,
                'count' => (int) ($c[$keyCount] ?? 0),
                'last' => $c[$keyLast] ?? null,
                'balance_cruzado' => $balanceCruzado,
            ];
        });

        $minWeekly = $conConteos->min('weekly');
        $conMinWeekly = $conConteos->where('weekly', $minWeekly)->values();

        $maxBalance = $conMinWeekly->max('balance_cruzado');
        $conBalance = $conMinWeekly->where('balance_cruzado', $maxBalance)->values();

        $minCount = $conBalance->min('count');
        $conMinCount = $conBalance->where('count', $minCount)->values();

        $oldestLast = $conMinCount->filter(fn ($x) => $x['last'] !== null)->min('last');
        if ($oldestLast === null) {
            $elegibles = $conMinCount;
        } else {
            $elegibles = $conMinCount->filter(fn ($x) => $x['last'] === $oldestLast || $x['last'] === null)->values();
        }

        if ($elegibles->isEmpty()) {
            return $candidatos->first();
        }
        if ($elegibles->count() === 1) {
            return $elegibles->first()['alumno'];
        }

        mt_srand(crc32($fecha->format('Y-m-d').$rol));
        $idx = mt_rand(0, $elegibles->count() - 1);

        return $elegibles->get($idx)['alumno'];
    }
}
