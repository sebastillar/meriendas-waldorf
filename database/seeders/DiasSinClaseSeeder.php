<?php

namespace Database\Seeders;

use App\Domain\Models\DiaSinClase;
use Illuminate\Database\Seeder;

class DiasSinClaseSeeder extends Seeder
{
    public function run(): void
    {
        $fechas = [
            '2026-03-30',
            '2026-03-31',
            '2026-04-01',
            '2026-04-02',
            '2026-04-03',
            '2026-05-01',
            '2026-07-18',
            '2026-08-25',
        ];

        foreach ($fechas as $fecha) {
            DiaSinClase::firstOrCreate(['fecha' => $fecha], ['motivo' => null]);
        }
    }
}

