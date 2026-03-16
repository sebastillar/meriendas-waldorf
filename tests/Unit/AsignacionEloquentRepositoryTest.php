<?php

namespace Tests\Unit;

use App\Domain\Models\Asignacion;
use App\Domain\Models\Alumno;
use App\Domain\Models\Familia;
use App\Domain\Models\ConfiguracionCalendario;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsignacionEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_conteos_por_mes_por_alumno_hasta_funciona_en_sqlite()
    {
        $familia = Familia::create([
            'apellido' => 'Test',
            'nombre_madre' => null,
            'email_madre' => null,
            'nombre_padre' => null,
            'email_padre' => null,
            'activo' => true,
        ]);

        $alumno = Alumno::create([
            'familia_id' => $familia->id,
            'nombre' => 'Alumno Test',
            'fecha_cumpleanos' => null,
            'activo' => true,
        ]);

        ConfiguracionCalendario::create([
            'anio' => 2026,
            'fecha_inicio_clases' => '2026-01-01',
            'fecha_fin_clases' => '2026-12-31',
        ]);

        Asignacion::create([
            'fecha' => '2026-03-10',
            'alumno_fruta_id' => $alumno->id,
            'alumno_elaboracion_id' => $alumno->id,
            'cereal' => 'cereal-1',
            'estado' => 'planificada',
        ]);

        Asignacion::create([
            'fecha' => '2026-03-15',
            'alumno_fruta_id' => $alumno->id,
            'alumno_elaboracion_id' => $alumno->id,
            'cereal' => 'cereal-2',
            'estado' => 'planificada',
        ]);

        /** @var AsignacionRepositoryInterface $repo */
        $repo = $this->app->make(AsignacionRepositoryInterface::class);

        $hasta = Carbon::create(2026, 3, 31);
        $result = $repo->getConteosPorMesPorAlumnoHasta($hasta);

        $this->assertArrayHasKey($alumno->id, $result);
        $this->assertArrayHasKey('2026-3', $result[$alumno->id]);
        $this->assertSame(2, $result[$alumno->id]['2026-3']['fruta']);
        $this->assertSame(2, $result[$alumno->id]['2026-3']['elaboracion']);
    }
}

