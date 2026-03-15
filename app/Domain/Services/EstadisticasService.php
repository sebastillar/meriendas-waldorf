<?php

namespace App\Domain\Services;

use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use App\Domain\Repositories\FamiliaRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EstadisticasService
{
    public function __construct(
        private AsignacionRepositoryInterface $asignacionRepository,
        private FamiliaRepositoryInterface $familiaRepository,
        private AlumnoRepositoryInterface $alumnoRepository
    ) {}

    /**
     * Estadísticas de una familia hasta la fecha: veces fruta, veces elaboración, cumpleaños, si recolecta para regalo.
     *
     * @return array{veces_fruta: int, veces_elaboracion: int, fecha_cumpleanos: ?string, recolectando_para_regalo: bool, apellido: string} apellido = nombre familia para listado
     */
    public function estadisticasFamilia(int $familiaId): array
    {
        $familia = $this->familiaRepository->find($familiaId);
        if (!$familia) {
            return [
                'veces_fruta' => 0,
                'veces_elaboracion' => 0,
                'fecha_cumpleanos' => null,
                'recolectando_para_regalo' => false,
                'apellido' => '',
            ];
        }
        $alumnoIds = $familia->alumnos->pluck('id')->all();
        $conteos = $this->asignacionRepository->getConteosPorAlumnoHasta(Carbon::today());
        $vecesFruta = 0;
        $vecesElaboracion = 0;
        foreach ($alumnoIds as $aid) {
            $c = $conteos[$aid] ?? ['fruta' => 0, 'elaboracion' => 0];
            $vecesFruta += (int) ($c['fruta'] ?? 0);
            $vecesElaboracion += (int) ($c['elaboracion'] ?? 0);
        }
        $primerCumpleanos = $familia->alumnos
            ->sortBy('id')
            ->first(fn ($a) => $a->fecha_cumpleanos !== null);
        return [
            'veces_fruta' => $vecesFruta,
            'veces_elaboracion' => $vecesElaboracion,
            'fecha_cumpleanos' => $primerCumpleanos?->fecha_cumpleanos?->format('d/m/Y'),
            'recolectando_para_regalo' => $familia->familia_regalo_id !== null,
            'apellido' => $familia->nombre_para_listado,
        ];
    }

    /**
     * Resumen por alumno: total fruta, total elaboración, y por mes opcional.
     * @return array<int, array{alumno_id: int, nombre: string, fruta: int, elaboracion: int, por_mes: array}>
     */
    public function resumenPorAlumno(?int $anio = null, ?int $mes = null): array
    {
        $hasta = $anio && $mes
            ? Carbon::createFromDate($anio, $mes, 1)->endOfMonth()
            : Carbon::today()->addYear();
        $conteos = $this->asignacionRepository->getConteosPorAlumnoHasta($hasta);
        $conteosPorMes = $this->asignacionRepository->getConteosPorMesPorAlumnoHasta($hasta);

        $alumnos = $this->alumnoRepository->todos()->keyBy('id');
        $out = [];
        foreach ($conteos as $alumnoId => $c) {
            $a = $alumnos->get($alumnoId);
            $porMes = $conteosPorMes[$alumnoId] ?? [];
            ksort($porMes);
            $out[] = [
                'alumno_id' => $alumnoId,
                'nombre' => $a ? $a->nombre : (string) $alumnoId,
                'fruta' => $c['fruta'],
                'elaboracion' => $c['elaboracion'],
                'por_mes' => $porMes,
            ];
        }
        return $out;
    }
}
