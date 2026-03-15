<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Familia;
use Illuminate\Support\Collection;

interface FamiliaRepositoryInterface
{
    public function find(int $id): ?Familia;

    /** @return Collection<int, Familia> */
    public function todos(): Collection;

    /** @return Collection<int, Familia> */
    public function activas(): Collection;

    public function guardar(Familia $familia): Familia;
}
