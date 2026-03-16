<?php

namespace App\Domain\Services;

use App\Domain\Models\ConfiguracionCalendario;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use App\Domain\Repositories\DiaSinClaseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AgendaService
{
    public function __construct(
        private AsignacionRepositoryInterface $asignacionRepository,
        private DiaSinClaseRepositoryInterface $diaSinClaseRepository
    ) {}

    /**
     * Agenda semanal. Si $alumnoId está definido, solo se devuelven las filas donde ese alumno tiene fruta o elaboración.
     * @return array<int, array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool, etiqueta_feriado: string}>
     */
    public function agendaSemana(?Carbon $fechaInicio = null, ?int $alumnoId = null): array
    {
        $inicio = $fechaInicio ? $fechaInicio->copy()->startOfDay() : Carbon::today()->startOfWeek();
        $config = ConfiguracionCalendario::where('anio', $inicio->year)->first();
        if ($config && $config->fecha_inicio_clases && $config->fecha_inicio_clases->gt($inicio)) {
            $inicio = $config->fecha_inicio_clases->copy()->startOfDay();
        }
        if ($config && $config->fecha_fin_clases && $config->fecha_fin_clases->lt($inicio)) {
            return [];
        }
        $fin = $inicio->copy()->addDays(6);
        if ($config && $config->fecha_fin_clases && $config->fecha_fin_clases->lt($fin)) {
            $fin = $config->fecha_fin_clases->copy()->startOfDay();
        }
        $filas = $this->construirAgenda($inicio, $fin);
        return $alumnoId !== null ? $this->filtrarFilasPorAlumno($filas, $alumnoId) : $filas;
    }

    /**
     * Agenda mensual. Si $alumnoId está definido, solo se devuelven las filas donde ese alumno tiene fruta o elaboración.
     * @return array<int, array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool, etiqueta_feriado: string}>
     */
    public function agendaMes(int $anio, int $mes, ?int $alumnoId = null): array
    {
        $inicio = Carbon::createFromDate($anio, $mes, 1)->startOfDay();
        $config = ConfiguracionCalendario::where('anio', $anio)->first();
        if ($config && $config->fecha_inicio_clases && $config->fecha_inicio_clases->gt($inicio)) {
            $inicio = $config->fecha_inicio_clases->copy()->startOfDay();
        }
        $finMes = Carbon::createFromDate($anio, $mes, 1)->endOfMonth();
        if ($config && $config->fecha_fin_clases && $config->fecha_fin_clases->lt($inicio)) {
            return [];
        }
        if ($config && $config->fecha_fin_clases && $config->fecha_fin_clases->lt($finMes)) {
            $fin = $config->fecha_fin_clases->copy()->startOfDay();
        } else {
            $fin = $finMes;
        }
        $filas = $this->construirAgenda($inicio, $fin);
        return $alumnoId !== null ? $this->filtrarFilasPorAlumno($filas, $alumnoId) : $filas;
    }

    /**
     * Filtra las filas de agenda dejando solo las que tienen al alumno en fruta o elaboración.
     * @param array<int, array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool, etiqueta_feriado: string}> $filas
     * @return array<int, array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool, etiqueta_feriado: string}>
     */
    public function filtrarFilasPorAlumno(array $filas, int $alumnoId): array
    {
        $id = (int) $alumnoId;
        return array_values(array_filter($filas, function (array $fila) use ($id): bool {
            $idFruta = (int) ($fila['fruta']['id'] ?? 0);
            $idElab = (int) ($fila['elaboracion']['id'] ?? 0);
            return $idFruta === $id || $idElab === $id;
        }));
    }

    private function construirAgenda(Carbon $inicio, Carbon $fin): array
    {
        $asignaciones = $this->asignacionRepository->getEntreFechas($inicio, $fin)->keyBy(fn ($a) => $a->fecha->toDateString());
        $diasSinClase = $this->diaSinClaseRepository->fechasEntre($inicio, $fin)->map(fn ($d) => $d->toDateString())->flip();

        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
        $out = [];
        $cursor = $inicio->copy();
        while ($cursor->lte($fin)) {
            $key = $cursor->toDateString();
            $asig = $asignaciones->get($key);
            $esFeriado = $cursor->isWeekend() || $diasSinClase->has($key);
            $etiquetaFeriado = $esFeriado ? ($cursor->isWeekend() ? 'Fin de semana' : 'Día sin clase') : '';
            // dayOfWeek: 0=domingo, 1=lunes..6=sábado -> índice 0=lunes..6=domingo
            $indiceDia = $cursor->dayOfWeek === 0 ? 6 : $cursor->dayOfWeek - 1;
            $out[] = [
                'fecha' => $key,
                'dia' => $dias[$indiceDia],
                'cereal' => $asig?->cereal ?? '',
                'fruta' => $asig ? ['id' => $asig->alumno_fruta_id, 'nombre' => $asig->alumnoFruta?->nombre ?? ''] : [],
                'elaboracion' => $asig ? ['id' => $asig->alumno_elaboracion_id, 'nombre' => $asig->alumnoElaboracion?->nombre ?? ''] : [],
                'es_feriado' => $esFeriado,
                'etiqueta_feriado' => $etiquetaFeriado,
            ];
            $cursor->addDay();
        }
        return $out;
    }

    /**
     * Agenda de un solo día (para iCal por día).
     * @return array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool, etiqueta_feriado: string}|null
     */
    public function agendaUnDia(Carbon $fecha): ?array
    {
        $filas = $this->construirAgenda($fecha->copy()->startOfDay(), $fecha->copy()->startOfDay());
        return $filas[0] ?? null;
    }

    /**
     * Devuelve el rol del alumno en la fecha dada ('fruta', 'elaboracion') o null si no está asignado.
     */
    public function getRolAlumnoEnFecha(int $alumnoId, Carbon $fecha): ?string
    {
        $asig = $this->asignacionRepository->getPorFecha($fecha->copy()->startOfDay());
        if (!$asig) {
            return null;
        }
        $idFruta = (int) $asig->alumno_fruta_id;
        $idElab = (int) $asig->alumno_elaboracion_id;
        if ($idFruta === $alumnoId) {
            return 'fruta';
        }
        if ($idElab === $alumnoId) {
            return 'elaboracion';
        }
        return null;
    }

    /**
     * Avisos de próximos turnos para un alumno (hoy, mañana, próximo lunes si aplica).
     * @return array<int, array{mensaje: string, fecha: string}>
     */
    public function getAvisosProximosParaAlumno(int $alumnoId): array
    {
        $hoy = Carbon::today();
        $avisos = [];

        $rolHoy = $this->getRolAlumnoEnFecha($alumnoId, $hoy);
        if ($rolHoy) {
            $avisos[] = [
                'mensaje' => 'Hoy te toca ' . ($rolHoy === 'fruta' ? 'fruta' : 'elaboración') . '.',
                'fecha' => $hoy->format('d/m/Y'),
            ];
        }

        $manana = $hoy->copy()->addDay();
        $rolManana = $this->getRolAlumnoEnFecha($alumnoId, $manana);
        if ($rolManana) {
            $avisos[] = [
                'mensaje' => 'Mañana te toca ' . ($rolManana === 'fruta' ? 'fruta' : 'elaboración') . '.',
                'fecha' => $manana->format('d/m/Y'),
            ];
        }

        if ($hoy->isFriday() || $hoy->isSaturday() || $hoy->isSunday()) {
            $proximoLunes = $hoy->copy()->next(Carbon::MONDAY);
            $rolLunes = $this->getRolAlumnoEnFecha($alumnoId, $proximoLunes);
            if ($rolLunes && $proximoLunes->ne($manana)) {
                $avisos[] = [
                    'mensaje' => 'El lunes ' . $proximoLunes->format('d/m') . ' te toca ' . ($rolLunes === 'fruta' ? 'fruta' : 'elaboración') . '.',
                    'fecha' => $proximoLunes->format('d/m/Y'),
                ];
            }
        }

        return $avisos;
    }
}
