<?php

namespace App\Domain\Repositories;

use App\Domain\Models\CerealPorDia;
use Illuminate\Support\Collection;

interface CerealPorDiaRepositoryInterface
{
    public function find(int $id): ?CerealPorDia;

    public function getPorDiaSemana(int $diaSemana): ?CerealPorDia;

    /** @return Collection<int, CerealPorDia> */
    public function todos(): Collection;

    /** @return Collection<int, CerealPorDia> */
    public function todosActivos(): Collection;

    public function guardar(CerealPorDia $cerealPorDia): CerealPorDia;

    public function eliminar(CerealPorDia $cerealPorDia): bool;
}
