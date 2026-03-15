<?php

namespace Database\Seeders;

use App\Domain\Models\Alumno;
use App\Domain\Models\Familia;
use Illuminate\Database\Seeder;

class FamiliasAlumnosSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            'Bruna',
            'Demián',
            'Emiliano',
            'Felipe',
            'Gael',
            'Joaquin',
            'Julieta',
            'Lisa',
            'Lorenzo',
            'Olivia',
            'Pedro',
            'Renato',
        ];

        foreach ($nombres as $nombre) {
            $familia = Familia::create([
                'activo' => true,
            ]);

            Alumno::create([
                'familia_id' => $familia->id,
                'nombre' => $nombre,
                'activo' => true,
            ]);
        }
    }
}

