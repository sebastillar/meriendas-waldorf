<?php

namespace App\Domain\Repositories;

interface ConfiguracionRepositoryInterface
{
    public function get(string $clave, mixed $default = null): mixed;

    public function set(string $clave, mixed $valor): void;
}
