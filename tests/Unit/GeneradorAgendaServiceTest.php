<?php

namespace Tests\Unit;

use App\Domain\Models\Alumno;
use App\Domain\Models\Asignacion;
use App\Domain\Models\ConfiguracionCalendario;
use App\Domain\Models\Familia;
use App\Domain\Services\GeneradorAgendaService;
use Carbon\Carbon;
use Database\Seeders\CerealesPorDiaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneradorAgendaServiceTest extends TestCase
{
    use RefreshDatabase;

    private function seedCereales(): void
    {
        $this->seed(CerealesPorDiaSeeder::class);
    }

    private function crearAlumno(string $nombre): Alumno
    {
        $familia = Familia::create([
            'nombre_madre' => null,
            'email_madre' => null,
            'nombre_padre' => null,
            'email_padre' => null,
            'activo' => true,
        ]);

        return Alumno::create([
            'familia_id' => $familia->id,
            'nombre' => $nombre,
            'fecha_cumpleanos' => null,
            'activo' => true,
        ]);
    }

    public function test_no_repite_mismo_rol_en_la_misma_semana_mientras_hay_alumnos_sin_ese_turno_en_la_semana(): void
    {
        $this->seedCereales();
        for ($i = 0; $i < 10; $i++) {
            $this->crearAlumno(sprintf('Alumno %02d', $i));
        }

        ConfiguracionCalendario::create([
            'anio' => 2026,
            'fecha_inicio_clases' => '2026-04-06',
            'fecha_fin_clases' => '2026-04-24',
        ]);

        $generador = app(GeneradorAgendaService::class);
        $generador->generarParaMes(2026, 4);

        $asignaciones = Asignacion::orderBy('fecha')->get();
        $this->assertNotEmpty($asignaciones);

        $porSemana = [];
        foreach ($asignaciones as $a) {
            $lunes = Carbon::parse($a->fecha)->startOfWeek(Carbon::MONDAY)->toDateString();
            if (! isset($porSemana[$lunes])) {
                $porSemana[$lunes] = ['fruta' => [], 'elaboracion' => []];
            }
            $porSemana[$lunes]['fruta'][$a->alumno_fruta_id] = ($porSemana[$lunes]['fruta'][$a->alumno_fruta_id] ?? 0) + 1;
            $porSemana[$lunes]['elaboracion'][$a->alumno_elaboracion_id] = ($porSemana[$lunes]['elaboracion'][$a->alumno_elaboracion_id] ?? 0) + 1;
        }

        foreach ($porSemana as $lunes => $roles) {
            foreach (['fruta', 'elaboracion'] as $rol) {
                foreach ($roles[$rol] as $alumnoId => $veces) {
                    $this->assertLessThanOrEqual(
                        1,
                        $veces,
                        "Alumno {$alumnoId} repitió {$rol} {$veces} veces en la semana del {$lunes}"
                    );
                }
            }
        }
    }

    public function test_precarga_semana_cruza_mes_no_asigna_elaboracion_a_quien_ya_elaboro_antes_en_la_misma_semana(): void
    {
        $this->seedCereales();

        $alpha = $this->crearAlumno('Alpha');
        $beta = $this->crearAlumno('Beta');
        $gamma = $this->crearAlumno('Gamma');

        // 2026-04-06 es lunes; 2026-04-07 es martes (misma semana). La regeneración empieza el 7: el 6 queda en BD.
        Asignacion::create([
            'fecha' => '2026-04-06',
            'alumno_fruta_id' => $beta->id,
            'alumno_elaboracion_id' => $alpha->id,
            'cereal' => 'Arroz',
            'estado' => 'planificada',
        ]);

        ConfiguracionCalendario::create([
            'anio' => 2026,
            'fecha_inicio_clases' => '2026-04-07',
            'fecha_fin_clases' => '2026-04-30',
        ]);

        $generador = app(GeneradorAgendaService::class);
        $n = $generador->generarParaMes(2026, 4);
        $this->assertGreaterThan(0, $n, 'Debería generarse al menos un día lectivo en abril');

        $apr7 = Asignacion::query()->whereDate('fecha', '2026-04-07')->first();
        $this->assertNotNull($apr7);
        $this->assertNotSame(
            $alpha->id,
            $apr7->alumno_elaboracion_id,
            'No debería tocarse elaboración otra vez en la misma semana mientras Gamma aún no elaboró en esa semana'
        );
    }
}
