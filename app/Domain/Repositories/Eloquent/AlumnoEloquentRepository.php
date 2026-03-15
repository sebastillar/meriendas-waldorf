<?php

namespace App\Domain\Repositories\Eloquent;

use App\Domain\Models\Alumno;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use Illuminate\Support\Collection;

class AlumnoEloquentRepository implements AlumnoRepositoryInterface
{
    public function find(int $id): ?Alumno
    {
        return Alumno::with('familia')->find($id);
    }

    public function todos(): Collection
    {
        return Alumno::with('familia')->orderBy('nombre')->get();
    }

    public function activos(): Collection
    {
        return Alumno::with('familia')->where('activo', true)->orderBy('nombre')->get();
    }

    public function guardar(Alumno $alumno): Alumno
    {
        $alumno->save();
        return $alumno;
    }

    public function eliminar(Alumno $alumno): bool
    {
        return $alumno->delete();
    }
}
