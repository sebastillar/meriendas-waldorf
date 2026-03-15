<?php

namespace App\Domain\Repositories\Eloquent;

use App\Domain\Models\Configuracion;
use App\Domain\Repositories\ConfiguracionRepositoryInterface;

class ConfiguracionEloquentRepository implements ConfiguracionRepositoryInterface
{
    public function get(string $clave, mixed $default = null): mixed
    {
        $c = Configuracion::where('clave', $clave)->first();
        return $c?->valor ?? $default;
    }

    public function set(string $clave, mixed $valor): void
    {
        Configuracion::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor === null ? null : (string) $valor]
        );
    }
}
