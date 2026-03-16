<?php

namespace App\Domain\Services;

use App\Domain\Models\ConfiguracionCalendario;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use App\Domain\Repositories\DiaSinClaseRepositoryInterface;
use Carbon\Carbon;

class ResumenMensualService
{
    public function __construct(
        private AlumnoRepositoryInterface $alumnoRepository,
        private AsignacionRepositoryInterface $asignacionRepository,
        private DiaSinClaseRepositoryInterface $diaSinClaseRepository
    ) {}

    /**
     * Cantidad de días del mes en que hay merienda (días laborables excluyendo fines de semana y días sin clase).
     */
    public function contarDiasConMeriendaEnMes(int $anio, int $mes): int
    {
        $desdeMes = Carbon::createFromDate($anio, $mes, 1)->startOfDay();
        $config = ConfiguracionCalendario::where('anio', $anio)->first();
        if ($config && $config->fecha_inicio_clases) {
            $inicioClasesCarbon = $config->fecha_inicio_clases->copy()->startOfDay();
            $desde = $inicioClasesCarbon->greaterThan($desdeMes) ? $inicioClasesCarbon : $desdeMes;
        } else {
            $desde = $desdeMes;
        }
        $hasta = $desde->copy()->endOfMonth();
        $fechasSinClase = $this->diaSinClaseRepository->fechasEntre($desde, $hasta)
            ->map(fn ($d) => $d->toDateString())->flip();

        $contador = 0;
        $cursor = $desde->copy();
        while ($cursor->lte($hasta)) {
            if ($cursor->isWeekday() && !$fechasSinClase->has($cursor->toDateString())) {
                $contador++;
            }
            $cursor->addDay();
        }
        return $contador;
    }

    /**
     * Resumen del mes: por cada alumno activo, veces que llevó fruta y veces que elaboró.
     *
     * @return array<int, array{nombre: string, apellido: string, veces_fruta: int, veces_elaboracion: int}> apellido = familia para listado
     */
    public function resumenParaMes(int $anio, int $mes): array
    {
        $finMes = Carbon::createFromDate($anio, $mes, 1)->endOfMonth();
        $conteos = $this->asignacionRepository->getConteosPorMesPorAlumnoHasta($finMes);
        $claveMes = "{$anio}-{$mes}";

        $alumnos = $this->alumnoRepository->activos();
        $filas = [];

        foreach ($alumnos as $alumno) {
            $porMes = $conteos[$alumno->id] ?? [];
            $datos = $porMes[$claveMes] ?? ['fruta' => 0, 'elaboracion' => 0];

            $filas[] = [
                'nombre' => $alumno->nombre,
                'apellido' => $alumno->familia?->nombre_para_listado ?? '',
                'veces_fruta' => (int) ($datos['fruta'] ?? 0),
                'veces_elaboracion' => (int) ($datos['elaboracion'] ?? 0),
            ];
        }

        usort($filas, function ($a, $b) {
            $cmp = strcasecmp($a['apellido'], $b['apellido']);
            return $cmp !== 0 ? $cmp : strcasecmp($a['nombre'], $b['nombre']);
        });

        return $filas;
    }
}
