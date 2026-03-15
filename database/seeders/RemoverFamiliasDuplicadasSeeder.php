<?php

namespace Database\Seeders;

use App\Domain\Models\Familia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Agrupa familias duplicadas (por apellido si existe, si no por nombre del primer hijo) y mantiene una por grupo.
 * Reasigna alumnos, usuarios y referencias familia_regalo_id a la familia que se conserva.
 */
class RemoverFamiliasDuplicadasSeeder extends Seeder
{
    public function run(): void
    {
        $familias = Familia::with(['alumnos' => fn ($q) => $q->orderBy('id')])->orderBy('id')->get();

        $apellidosPorId = [];
        if (Schema::hasColumn('familias', 'apellido')) {
            $apellidosPorId = \Illuminate\Support\Facades\DB::table('familias')->pluck('apellido', 'id')->all();
        }

        $grupos = [];
        foreach ($familias as $familia) {
            $clave = isset($apellidosPorId[$familia->id])
                ? $apellidosPorId[$familia->id]
                : ($familia->alumnos->first()?->nombre ?? 'sin_nombre_' . $familia->id);
            $grupos[$clave] = $grupos[$clave] ?? [];
            $grupos[$clave][] = $familia;
        }

        foreach ($grupos as $familiasGrupo) {
            if (count($familiasGrupo) < 2) {
                continue;
            }

            $mantener = $familiasGrupo[0];
            $eliminar = array_slice($familiasGrupo, 1);

            foreach ($eliminar as $dup) {
                foreach ($dup->alumnos as $alumno) {
                    $alumno->familia_id = $mantener->id;
                    $alumno->save();
                }

                User::where('familia_id', $dup->id)->update(['familia_id' => $mantener->id]);
                Familia::where('familia_regalo_id', $dup->id)->update(['familia_regalo_id' => $mantener->id]);

                $dup->delete();
            }
        }
    }
}
