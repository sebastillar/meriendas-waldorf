<?php

namespace Database\Seeders;

use App\Domain\Models\CerealPorDia;
use Illuminate\Database\Seeder;

class CerealesPorDiaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            1 => 'Arroz',
            2 => 'Cebada',
            3 => 'Mijo',
            4 => 'Centeno',
            5 => 'Avena',
        ];

        foreach ($data as $dia => $cereal) {
            CerealPorDia::updateOrCreate(
                ['dia_semana' => $dia],
                ['cereal' => $cereal, 'activo' => true],
            );
        }
    }
}

