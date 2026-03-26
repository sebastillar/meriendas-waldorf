<?php

namespace Tests\Feature;

use App\Domain\Models\Alumno;
use App\Domain\Models\Familia;
use App\Domain\Services\RecolectandoService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CumpleanosPageTest extends TestCase
{
    public function test_cumpleanos_page_renders_table_and_banner(): void
    {
        $alumno = new Alumno();
        $alumno->id = 11;
        $alumno->nombre = 'Olivia';
        $alumno->familia_id = 31;

        $alumnoDos = new Alumno();
        $alumnoDos->id = 12;
        $alumnoDos->nombre = 'Mateo';
        $alumnoDos->familia_id = 32;

        $familiaRecolectora = new Familia();
        $familiaRecolectora->id = 21;
        $familiaRecolectora->setRelation('alumnos', new Collection([$alumno]));

        $this->mock(RecolectandoService::class, function ($mock) use ($alumno, $alumnoDos, $familiaRecolectora): void {
            $mock->shouldReceive('recolectasDelMesActual')
                ->once()
                ->andReturn([
                    [
                        'alumno_beneficiario' => $alumno,
                        'familia_beneficiaria' => new Familia(),
                        'familia_recolectora' => $familiaRecolectora,
                        'fecha_cumpleanos' => Carbon::create(2026, 3, 27)->startOfDay(),
                        'estado' => 'activa',
                        'aportaron_count' => 3,
                        'total_count' => 10,
                    ],
                    [
                        'alumno_beneficiario' => $alumnoDos,
                        'familia_beneficiaria' => new Familia(),
                        'familia_recolectora' => $familiaRecolectora,
                        'fecha_cumpleanos' => Carbon::create(2026, 3, 30)->startOfDay(),
                        'estado' => 'activa',
                        'aportaron_count' => 4,
                        'total_count' => 10,
                    ],
                ]);

            $mock->shouldReceive('cumpleanosConFamiliaEncargada')
                ->once()
                ->andReturn([
                    [
                        'alumno' => $alumno,
                        'fecha_cumpleanos' => Carbon::create(2026, 3, 27)->startOfDay(),
                        'familia_encargada' => $familiaRecolectora,
                    ],
                ]);
        });

        $response = $this->get('/cumpleanos');

        $response->assertOk();
        $response->assertSee('Cumpleaños', false);
        $response->assertSee('Colectas en curso', false);
        $response->assertSee('Olivia', false);
        $response->assertSee('Mateo', false);
        $response->assertSee('27/03', false);
    }
}
