<?php

namespace App\Domain\Repositories\Eloquent;

use App\Domain\Models\CerealPorDia;
use App\Domain\Repositories\CerealPorDiaRepositoryInterface;
use Illuminate\Support\Collection;

class CerealPorDiaEloquentRepository implements CerealPorDiaRepositoryInterface
{
    public function find(int $id): ?CerealPorDia
    {
        return CerealPorDia::find($id);
    }

    public function getPorDiaSemana(int $diaSemana): ?CerealPorDia
    {
        return CerealPorDia::where('dia_semana', $diaSemana)->where('activo', true)->first();
    }

    public function todos(): Collection
    {
        return CerealPorDia::orderBy('dia_semana')->get();
    }

    public function todosActivos(): Collection
    {
        return CerealPorDia::where('activo', true)->orderBy('dia_semana')->get();
    }

    public function guardar(CerealPorDia $cerealPorDia): CerealPorDia
    {
        $cerealPorDia->save();
        return $cerealPorDia;
    }

    public function eliminar(CerealPorDia $cerealPorDia): bool
    {
        return $cerealPorDia->delete();
    }
}
