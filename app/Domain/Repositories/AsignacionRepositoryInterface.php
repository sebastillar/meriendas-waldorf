<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Asignacion;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface AsignacionRepositoryInterface
{
    /**
     * @return array<int, array{fruta: int, elaboracion: int, ultima_fruta: ?string, ultima_elaboracion: ?string}>
     */
    public function getConteosPorAlumnoHasta(Carbon $fecha): array;

    /**
     * Conteos por mes por alumno hasta la fecha. Clave alumno_id, valor array de 'anio-mes' => ['fruta' => int, 'elaboracion' => int].
     * @return array<int, array<string, array{fruta: int, elaboracion: int}>>
     */
    public function getConteosPorMesPorAlumnoHasta(Carbon $fecha): array;

    public function find(int $id): ?Asignacion;

    public function getPorFecha(Carbon $fecha): ?Asignacion;

    /** @return Collection<int, Asignacion> */
    public function getEntreFechas(Carbon $desde, Carbon $hasta): Collection;

    /** @return Collection<int, Asignacion> */
    public function getFuturasDesde(Carbon $desde): Collection;

    public function guardar(Asignacion $asignacion): Asignacion;

    public function eliminar(Asignacion $asignacion): bool;

    public function eliminarFuturasDesde(Carbon $desde): int;
}
