<?php

namespace Database\Seeders;

use App\Domain\Models\Alumno;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Elimina alumnos duplicados: mismo familia_id + nombre. Conserva uno por grupo (el de menor id).
 * Reasigna asignaciones que referencien al duplicado hacia el alumno que se conserva.
 */
class RemoverAlumnosDuplicadosSeeder extends Seeder
{
    public function run(): void
    {
        $grupos = Alumno::orderBy('id')->get()->groupBy(fn (Alumno $a) => $a->familia_id . '|' . $a->nombre);

        foreach ($grupos as $clave => $alumnos) {
            if ($alumnos->count() < 2) {
                continue;
            }
            $mantener = $alumnos->sortBy('id')->first();
            $eliminar = $alumnos->reject(fn (Alumno $a) => $a->id === $mantener->id);

            foreach ($eliminar as $dup) {
                DB::table('asignaciones')
                    ->where('alumno_fruta_id', $dup->id)
                    ->update(['alumno_fruta_id' => $mantener->id]);
                DB::table('asignaciones')
                    ->where('alumno_elaboracion_id', $dup->id)
                    ->update(['alumno_elaboracion_id' => $mantener->id]);
                if (Schema::hasTable('intercambios')) {
                    DB::table('intercambios')
                        ->where('alumno_original_id', $dup->id)
                        ->update(['alumno_original_id' => $mantener->id]);
                    DB::table('intercambios')
                        ->where('alumno_nuevo_id', $dup->id)
                        ->update(['alumno_nuevo_id' => $mantener->id]);
                }
                if (Schema::hasTable('recolecta_aportes')) {
                    DB::table('recolecta_aportes')->where('alumno_id', $dup->id)->delete();
                }
                $dup->delete();
            }
        }
    }
}
