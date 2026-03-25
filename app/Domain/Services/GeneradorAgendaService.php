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

        DB::transaction(function () use ($diasLectivos, $alumnosActivos, $desdeRegeneracion, &$conteosSemana, &$generadas) {
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
                $conteosHasta = $this->asignacionRepository->getConteosPorAlumnoHasta($fecha->copy()->subDay());

                $alumnoFruta = $this->elegirAlumnoParaRol($alumnosActivos, $conteosHasta, $conteosSemana, $weekKey, 'fruta', $fecha, null);
                $alumnoElab = $this->elegirAlumnoParaRol($alumnosActivos, $conteosHasta, $conteosSemana, $weekKey, 'elaboracion', $fecha, $alumnoFruta?->id);

                if ($alumnoFruta && $alumnoElab) {
                    $asignacion = new Asignacion([
                        'fecha' => $fecha,
                        'alumno_fruta_id' => $alumnoFruta->id,
                        'alumno_elaboracion_id' => $alumnoElab->id,
                        'cereal' => $cereal,
                        'estado' => 'planificada',
                    ]);
                    $this->asignacionRepository->guardar($asignacion);
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
     * @param  array<string, array{fruta: array<int, int>, elaboracion: array<int, int>}>  $conteosSemana
     */
    private function elegirAlumnoParaRol(
        Collection $alumnos,
        array $conteosHasta,
        array $conteosSemana,
        string $weekKey,
        string $rol,
        Carbon $fecha,
        ?int $excluirAlumnoId
    ): ?Alumno {
        $keyCount = $rol === 'fruta' ? 'fruta' : 'elaboracion';
        $keyLast = $rol === 'fruta' ? 'ultima_fruta' : 'ultima_elaboracion';

        $candidatos = $alumnos->filter(fn ($a) => $a->id !== $excluirAlumnoId)->values();
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
            // Equilibrio entre roles: a quien le "falta" más este rol respecto del otro (desempate entre empates en conteo del rol).
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

        $minCount = $conMinWeekly->min('count');
        $conMinCount = $conMinWeekly->where('count', $minCount)->values();

        $maxBalance = $conMinCount->max('balance_cruzado');
        $conBalance = $conMinCount->where('balance_cruzado', $maxBalance)->values();

        $oldestLast = $conBalance->filter(fn ($x) => $x['last'] !== null)->min('last');
        if ($oldestLast === null) {
            $elegibles = $conBalance;
        } else {
            $elegibles = $conBalance->filter(fn ($x) => $x['last'] === $oldestLast || $x['last'] === null)->values();
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
