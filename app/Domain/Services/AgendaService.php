<?php

namespace App\Domain\Services;

use App\Domain\Models\ConfiguracionCalendario;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use App\Domain\Repositories\DiaSinClaseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AgendaService
{
    public function __construct(
        private AsignacionRepositoryInterface $asignacionRepository,
        private DiaSinClaseRepositoryInterface $diaSinClaseRepository,
        private AlumnoRepositoryInterface $alumnoRepository
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
        $rango = $this->rangoCalendarioMes($anio, $mes);
        if ($rango === null) {
            return [];
        }
        [$inicio, $fin] = $rango;
        $filas = $this->construirAgenda($inicio, $fin);
        return $alumnoId !== null ? $this->filtrarFilasPorAlumno($filas, $alumnoId) : $filas;
    }

    /**
     * Conteos de fruta y elaboración por alumno activo en el mismo rango de fechas que la vista mensual de la agenda.
     *
     * @return array{filas: array<int, array{id: int, nombre: string, elaboracion: int, fruta: int, total: int}>, dias_con_plan: int, periodo_etiqueta: string}
     */
    public function estadisticasResumenMes(int $anio, int $mes): array
    {
        $rango = $this->rangoCalendarioMes($anio, $mes);
        if ($rango === null) {
            return [
                'filas' => [],
                'dias_con_plan' => 0,
                'periodo_etiqueta' => '',
            ];
        }
        [$inicio, $fin] = $rango;
        $asignaciones = $this->asignacionRepository->getEntreFechas($inicio, $fin);

        $frutaPorId = [];
        $elabPorId = [];
        foreach ($asignaciones as $a) {
            $fid = (int) $a->alumno_fruta_id;
            $eid = (int) $a->alumno_elaboracion_id;
            $frutaPorId[$fid] = ($frutaPorId[$fid] ?? 0) + 1;
            $elabPorId[$eid] = ($elabPorId[$eid] ?? 0) + 1;
        }

        $filas = [];
        foreach ($this->alumnoRepository->activos()->sortBy(fn ($a) => mb_strtolower($a->nombre ?? '')) as $alumno) {
            $f = (int) ($frutaPorId[$alumno->id] ?? 0);
            $e = (int) ($elabPorId[$alumno->id] ?? 0);
            $filas[] = [
                'id' => $alumno->id,
                'nombre' => $alumno->nombre,
                'elaboracion' => $e,
                'fruta' => $f,
                'total' => $e + $f,
            ];
        }

        // isoFormat (tokens tipo Moment); evitar translatedFormat('d MMM Y') que mezcla letras y deja "abril" + mes numérico.
        $periodoEtiqueta = $inicio->locale('es')->isoFormat('DD [de] MMMM [de] YYYY')
            . ' – '
            . $fin->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');

        return [
            'filas' => $filas,
            'dias_con_plan' => $asignaciones->count(),
            'periodo_etiqueta' => $periodoEtiqueta,
        ];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}|null [inicio, fin] inclusive; null si el mes queda fuera del calendario escolar.
     */
    private function rangoCalendarioMes(int $anio, int $mes): ?array
    {
        $inicio = Carbon::createFromDate($anio, $mes, 1)->startOfDay();
        $config = ConfiguracionCalendario::where('anio', $anio)->first();
        if ($config && $config->fecha_inicio_clases && $config->fecha_inicio_clases->gt($inicio)) {
            $inicio = $config->fecha_inicio_clases->copy()->startOfDay();
        }
        $finMes = Carbon::createFromDate($anio, $mes, 1)->endOfMonth();
        if ($config && $config->fecha_fin_clases && $config->fecha_fin_clases->lt($inicio)) {
            return null;
        }
        if ($config && $config->fecha_fin_clases && $config->fecha_fin_clases->lt($finMes)) {
            $fin = $config->fecha_fin_clases->copy()->startOfDay();
        } else {
            $fin = $finMes;
        }

        return [$inicio, $fin];
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
     * Próximo día lectivo con merienda asignada.
     * Si son antes de las 15:00 se evalúa desde hoy; si no, desde mañana.
     * Añade 'etiqueta' (Hoy/Mañana/El {día}), 'familia_fruta' y 'familia_elaboracion'.
     *
     * @return array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool, etiqueta_feriado: string, etiqueta: string, familia_fruta: string, familia_elaboracion: string}|null
     */
    public function proximaDiaLectivoConMerienda(): ?array
    {
        $ahora = Carbon::now();
        $hoy = $ahora->copy()->startOfDay();
        $cursor = $ahora->hour < 15 ? $hoy->copy() : $hoy->copy()->addDay();

        for ($i = 0; $i <= 30; $i++) {
            $fecha = $cursor->copy()->addDays($i);

            if ($fecha->isWeekend()) {
                continue;
            }

            $fila = $this->agendaUnDia($fecha);

            if (!$fila || $fila['es_feriado'] || ($fila['cereal'] ?? '') === '') {
                continue;
            }

            $etiqueta = match (true) {
                $fecha->isSameDay($hoy) => 'Hoy',
                $fecha->isSameDay($hoy->copy()->addDay()) => 'Mañana',
                default => 'El ' . ucfirst($fila['dia']),
            };

            $frutaId = $fila['fruta']['id'] ?? null;
            $elabId  = $fila['elaboracion']['id'] ?? null;
            $alumnoFruta = $frutaId ? $this->alumnoRepository->find((int) $frutaId) : null;
            $alumnoElab  = $elabId  ? $this->alumnoRepository->find((int) $elabId)  : null;

            return array_merge($fila, [
                'etiqueta'            => $etiqueta,
                'familia_fruta'       => $alumnoFruta?->familia?->nombre_para_listado ?? '',
                'familia_elaboracion' => $alumnoElab?->familia?->nombre_para_listado ?? '',
            ]);
        }

        return null;
    }

    /**
     * Agenda semanal con nombres de familia añadidos a fruta y elaboración.
     * Cada fila no-feriado tendrá fruta['familia_nombre'] y elaboracion['familia_nombre'].
     *
     * @return array<int, array>
     */
    public function agendaSemanaConFamilias(?Carbon $fechaInicio = null): array
    {
        $filas = $this->agendaSemana($fechaInicio);

        return array_map(function (array $fila): array {
            if ($fila['es_feriado']) {
                return $fila;
            }

            $frutaId = $fila['fruta']['id'] ?? null;
            $elabId  = $fila['elaboracion']['id'] ?? null;
            $alumnoFruta = $frutaId ? $this->alumnoRepository->find((int) $frutaId) : null;
            $alumnoElab  = $elabId  ? $this->alumnoRepository->find((int) $elabId)  : null;

            $fila['fruta']['familia_nombre']       = $alumnoFruta?->familia?->nombre_para_listado ?? '';
            $fila['elaboracion']['familia_nombre']  = $alumnoElab?->familia?->nombre_para_listado ?? '';

            return $fila;
        }, $filas);
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
