<?php

namespace App\Domain\Repositories\Eloquent;

use App\Domain\Models\Familia;
use App\Domain\Repositories\FamiliaRepositoryInterface;
use Illuminate\Support\Collection;

class FamiliaEloquentRepository implements FamiliaRepositoryInterface
{
    public function find(int $id): ?Familia
    {
        return Familia::with('alumnos')->find($id);
    }

    public function todos(): Collection
    {
        return Familia::with('alumnos')->orderBy('id')->get();
    }

    public function activas(): Collection
    {
        return Familia::with('alumnos')->where('activo', true)->orderBy('id')->get();
    }

    public function guardar(Familia $familia): Familia
    {
        $familia->save();
        return $familia;
    }
}
