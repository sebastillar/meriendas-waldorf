<?php

namespace Tests\Feature;

use App\Domain\Models\Alumno;
use App\Domain\Models\Familia;
use App\Domain\Services\RecolectandoService;
use App\Filament\Widgets\RecolectandoWidget;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RecolectandoWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_muestra_todas_las_colectas_del_mes_actual()
    {
        $familiaBeneficiaria1 = new Familia();
        $familiaBeneficiaria1->id = 1;

        $familiaBeneficiaria2 = new Familia();
        $familiaBeneficiaria2->id = 2;

        $familiaRecolectora = new Familia();
        $familiaRecolectora->id = 10;
        $familiaRecolectora->nombre_para_listado = 'Familia Recolectora';

        $alumno1 = new Alumno();
        $alumno1->id = 100;
        $alumno1->nombre = 'Alumno 1';

        $alumno2 = new Alumno();
        $alumno2->id = 101;
        $alumno2->nombre = 'Alumno 2';

        $this->mock(RecolectandoService::class, function ($mock) use (
            $alumno1,
            $alumno2,
            $familiaBeneficiaria1,
            $familiaBeneficiaria2,
            $familiaRecolectora
        ) {
            $mock->shouldReceive('recolectasDelMesActual')
                ->andReturn([
                    [
                        'estado' => 'sin_recolectora',
                        'fecha_cumpleanos' => Carbon::create(2026, 3, 5)->startOfDay(),
                        'alumno_beneficiario' => $alumno1,
                        'familia_beneficiaria' => $familiaBeneficiaria1,
                        'familia_recolectora' => null,
                        'aportaron_count' => 0,
                        'total_count' => 10,
                    ],
                    [
                        'estado' => 'activa',
                        'fecha_cumpleanos' => Carbon::create(2026, 3, 20)->startOfDay(),
                        'alumno_beneficiario' => $alumno2,
                        'familia_beneficiaria' => $familiaBeneficiaria2,
                        'familia_recolectora' => $familiaRecolectora,
                        'aportaron_count' => 2,
                        'total_count' => 10,
                    ],
                ]);

            $mock->shouldReceive('proximoCumpleanosSinRecolectora')->never();
        });

        Livewire::test(RecolectandoWidget::class)
            ->assertSee('Alumno 1')
            ->assertSee('Alumno 2')
            ->assertSee('Sin recolectora')
            ->assertSee('Para que aparezca la colecta aquí');
    }
}

