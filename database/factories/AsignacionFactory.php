<?php

namespace Database\Factories;

use App\Domain\Models\Asignacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asignacion>
 */
class AsignacionFactory extends Factory
{
    protected $model = Asignacion::class;

    public function definition(): array
    {
        return [
            'fecha' => $this->faker->date(),
            'cereal' => $this->faker->word(),
            'estado' => 'activa',
        ];
    }
}

