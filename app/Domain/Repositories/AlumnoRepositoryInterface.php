<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Alumno;
use Illuminate\Support\Collection;

interface AlumnoRepositoryInterface
{
    public function find(int $id): ?Alumno;

    /** @return Collection<int, Alumno> */
    public function todos(): Collection;

    /** @return Collection<int, Alumno> */
    public function activos(): Collection;

    public function guardar(Alumno $alumno): Alumno;

    public function eliminar(Alumno $alumno): bool;
}
