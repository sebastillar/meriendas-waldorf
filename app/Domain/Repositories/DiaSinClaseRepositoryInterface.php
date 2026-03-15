<?php

namespace App\Domain\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface DiaSinClaseRepositoryInterface
{
    /** @return Collection<int, Carbon> Fechas sin clase entre desde y hasta */
    public function fechasEntre(Carbon $desde, Carbon $hasta): Collection;

    public function esDiaSinClase(Carbon $fecha): bool;
}
