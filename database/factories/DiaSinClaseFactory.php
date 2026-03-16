<?php

namespace Database\Factories;

use App\Domain\Models\DiaSinClase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiaSinClase>
 */
class DiaSinClaseFactory extends Factory
{
    protected $model = DiaSinClase::class;

    public function definition(): array
    {
        return [
            'fecha' => $this->faker->date(),
            'motivo' => $this->faker->sentence(),
        ];
    }
}

