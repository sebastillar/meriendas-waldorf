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

        $familiaRecolectora = new Familia();
        $familiaRecolectora->id = 21;
        $familiaRecolectora->setRelation('alumnos', new Collection([$alumno]));

        $this->mock(RecolectandoService::class, function ($mock) use ($alumno, $familiaRecolectora): void {
            $mock->shouldReceive('recolectaActual')
                ->once()
                ->andReturn([
                    'familia_recolectora' => $familiaRecolectora,
                    'alumno_beneficiario' => $alumno,
                    'aportaron_count' => 3,
                    'total_count' => 10,
                ]);

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
                ]);
        });

        $response = $this->get('/cumpleanos');

        $response->assertOk();
        $response->assertSee('Cumpleaños', false);
        $response->assertSee('Colecta en curso', false);
        $response->assertSee('Olivia', false);
        $response->assertSee('27/03/2026', false);
    }
}
