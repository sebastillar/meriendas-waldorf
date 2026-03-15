<?php

namespace App\Domain\Repositories\Eloquent;

use App\Domain\Models\DiaSinClase;
use App\Domain\Repositories\DiaSinClaseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DiaSinClaseEloquentRepository implements DiaSinClaseRepositoryInterface
{
    public function fechasEntre(Carbon $desde, Carbon $hasta): Collection
    {
        return DiaSinClase::whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->pluck('fecha')
            ->map(fn ($f) => Carbon::parse($f));
    }

    public function esDiaSinClase(Carbon $fecha): bool
    {
        return DiaSinClase::whereDate('fecha', $fecha)->exists();
    }
}
