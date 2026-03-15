<?php

namespace App\Domain\Repositories\Eloquent;

use App\Domain\Models\Asignacion;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AsignacionEloquentRepository implements AsignacionRepositoryInterface
{
    public function find(int $id): ?Asignacion
    {
        return Asignacion::with(['alumnoFruta.familia', 'alumnoElaboracion.familia'])->find($id);
    }

    public function getPorFecha(Carbon $fecha): ?Asignacion
    {
        $dateString = $fecha->toDateString();
        return Asignacion::with(['alumnoFruta.familia', 'alumnoElaboracion.familia'])
            ->where('fecha', $dateString)
            ->first();
    }

    public function getEntreFechas(Carbon $desde, Carbon $hasta): Collection
    {
        return Asignacion::with(['alumnoFruta.familia', 'alumnoElaboracion.familia'])
            ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->orderBy('fecha')
            ->get();
    }

    public function getFuturasDesde(Carbon $desde): Collection
    {
        return Asignacion::with(['alumnoFruta', 'alumnoElaboracion'])
            ->where('fecha', '>=', $desde->toDateString())
            ->orderBy('fecha')
            ->get();
    }

    public function guardar(Asignacion $asignacion): Asignacion
    {
        $asignacion->save();
        return $asignacion;
    }

    public function eliminar(Asignacion $asignacion): bool
    {
        return $asignacion->delete();
    }

    public function eliminarFuturasDesde(Carbon $desde): int
    {
        return Asignacion::where('fecha', '>=', $desde->toDateString())->delete();
    }

    public function getConteosPorAlumnoHasta(Carbon $fecha): array
    {
        $hasta = $fecha->toDateString();
        $fruta = Asignacion::where('fecha', '<=', $hasta)
            ->selectRaw('alumno_fruta_id as alumno_id, count(*) as total, max(fecha) as ultima')
            ->groupBy('alumno_fruta_id')
            ->get()
            ->keyBy('alumno_id');
        $elab = Asignacion::where('fecha', '<=', $hasta)
            ->selectRaw('alumno_elaboracion_id as alumno_id, count(*) as total, max(fecha) as ultima')
            ->groupBy('alumno_elaboracion_id')
            ->get()
            ->keyBy('alumno_id');

        $alumnoIds = $fruta->keys()->merge($elab->keys())->unique();
        $out = [];
        foreach ($alumnoIds as $id) {
            $r = $fruta->get($id);
            $e = $elab->get($id);
            $out[(int) $id] = [
                'fruta' => (int) ($r->total ?? 0),
                'elaboracion' => (int) ($e->total ?? 0),
                'ultima_fruta' => $r->ultima ?? null,
                'ultima_elaboracion' => $e->ultima ?? null,
            ];
        }
        return $out;
    }

    public function getConteosPorMesPorAlumnoHasta(Carbon $fecha): array
    {
        $hasta = $fecha->toDateString();
        $fruta = Asignacion::where('fecha', '<=', $hasta)
            ->selectRaw('alumno_fruta_id as alumno_id, YEAR(fecha) as anio, MONTH(fecha) as mes, count(*) as total')
            ->groupByRaw('alumno_fruta_id, YEAR(fecha), MONTH(fecha)')
            ->get();
        $elab = Asignacion::where('fecha', '<=', $hasta)
            ->selectRaw('alumno_elaboracion_id as alumno_id, YEAR(fecha) as anio, MONTH(fecha) as mes, count(*) as total')
            ->groupByRaw('alumno_elaboracion_id, YEAR(fecha), MONTH(fecha)')
            ->get();

        $out = [];
        foreach ($fruta as $r) {
            $id = (int) $r->alumno_id;
            $k = "{$r->anio}-{$r->mes}";
            if (!isset($out[$id])) {
                $out[$id] = [];
            }
            if (!isset($out[$id][$k])) {
                $out[$id][$k] = ['fruta' => 0, 'elaboracion' => 0];
            }
            $out[$id][$k]['fruta'] = (int) $r->total;
        }
        foreach ($elab as $r) {
            $id = (int) $r->alumno_id;
            $k = "{$r->anio}-{$r->mes}";
            if (!isset($out[$id])) {
                $out[$id] = [];
            }
            if (!isset($out[$id][$k])) {
                $out[$id][$k] = ['fruta' => 0, 'elaboracion' => 0];
            }
            $out[$id][$k]['elaboracion'] = (int) $r->total;
        }
        return $out;
    }
}
