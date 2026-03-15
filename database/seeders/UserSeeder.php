<?php

namespace Database\Seeders;

use App\Domain\Models\Familia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Crea un usuario por familia. Email y contraseña = nombre del primer niño sin tildes.
     * Ej.: niño "Demián" → email demian@meriendas, contraseña demian.
     */
    public function run(): void
    {
        $familias = Familia::with(['alumnos' => fn ($q) => $q->orderBy('nombre')])->get();

        foreach ($familias as $familia) {
            $primerNino = $familia->alumnos->first();
            if (!$primerNino) {
                continue;
            }

            $login = $this->nombreSinTildes($primerNino->nombre);
            if ($login === '') {
                continue;
            }

            $email = $this->emailUnico($login);
            $password = Hash::make($login);

            User::updateOrCreate(
                ['familia_id' => $familia->id],
                [
                    'email' => $email,
                    'name' => $primerNino->nombre,
                    'password' => $password,
                ]
            );
        }
    }

    /**
     * Nombre normalizado: minúsculas, sin tildes ni espacios (para login/contraseña).
     */
    private function nombreSinTildes(string $nombre): string
    {
        $s = Str::slug(Str::ascii($nombre), '');
        return strtolower($s);
    }

    /**
     * Genera un email único añadiendo sufijo numérico si ya existe.
     */
    private function emailUnico(string $login): string
    {
        $base = $login . '@meriendas';
        if (!User::where('email', $base)->exists()) {
            return $base;
        }
        $n = 2;
        do {
            $email = $login . $n . '@meriendas';
            $exists = User::where('email', $email)->exists();
            $n++;
        } while ($exists);
        return $email;
    }
}
