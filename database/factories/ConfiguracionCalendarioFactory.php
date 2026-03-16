<?php

namespace Database\Factories;

use App\Domain\Models\ConfiguracionCalendario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConfiguracionCalendario>
 */
class ConfiguracionCalendarioFactory extends Factory
{
    protected $model = ConfiguracionCalendario::class;

    public function definition(): array
    {
        $anio = (int) now()->year;

        return [
            'anio' => $anio,
            'fecha_inicio_clases' => "{$anio}-03-01",
            'fecha_fin_clases' => "{$anio}-12-20",
        ];
    }
}

