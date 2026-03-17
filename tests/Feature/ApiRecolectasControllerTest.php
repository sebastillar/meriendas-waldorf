<?php

namespace Tests\Feature;

use App\Domain\Models\Alumno;
use App\Domain\Models\Familia;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\FamiliaRepositoryInterface;
use App\Domain\Services\RecolectaAportesService;
use App\Domain\Services\RecolectandoService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ApiRecolectasControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mes_actual_returns_empty_list_when_no_alumnos()
    {
        $this->mock(AlumnoRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('activos')->once()->andReturn(new Collection());
        });

        $this->mock(FamiliaRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('activas')->andReturn(new Collection());
        });

        $this->mock(RecolectaAportesService::class, function ($mock) {
            $mock->shouldReceive('getAlumnoIdsQueAportaron')->andReturn([]);
            $mock->shouldReceive('alumnosParaAportes')->andReturn([]);
        });

        $this->app->bind(RecolectandoService::class, function ($app) {
            return new RecolectandoService(
                $app->make(AlumnoRepositoryInterface::class),
                $app->make(FamiliaRepositoryInterface::class),
                $app->make(RecolectaAportesService::class),
            );
        });

        $response = $this->getJson('/api/recolectas/cumpleanos/mes-actual');

        $response->assertOk();
        $response->assertExactJson(['data' => []]);
    }

    public function test_mes_actual_returns_multiple_rows_when_service_returns_multiple()
    {
        $familiaBeneficiaria1 = new Familia();
        $familiaBeneficiaria1->id = 1;

        $familiaBeneficiaria2 = new Familia();
        $familiaBeneficiaria2->id = 2;

        $familiaRecolectora = new Familia();
        $familiaRecolectora->id = 10;
        $familiaRecolectora->banco = 'Banco X';
        $familiaRecolectora->numero_cuenta = '123';
        $familiaRecolectora->tipo_cuenta = 'Caja de ahorro';
        $familiaRecolectora->nombre_cuenta = 'Titular';
        $familiaRecolectora->moneda = 'ARS';

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
                ->once()
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
        });

        $response = $this->getJson('/api/recolectas/cumpleanos/mes-actual');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.estado', 'sin_recolectora');
        $response->assertJsonPath('data.0.fecha_cumpleanos', '2026-03-05');
        $response->assertJsonPath('data.0.alumno_beneficiario.id', 100);
        $response->assertJsonPath('data.0.alumno_beneficiario.nombre', 'Alumno 1');
        $response->assertJsonPath('data.0.familia_beneficiaria.id', 1);
        $response->assertJsonPath('data.0.familia_recolectora', null);
        $response->assertJsonPath('data.0.aportaron_count', 0);
        $response->assertJsonPath('data.0.total_count', 10);

        $response->assertJsonPath('data.1.estado', 'activa');
        $response->assertJsonPath('data.1.fecha_cumpleanos', '2026-03-20');
        $response->assertJsonPath('data.1.alumno_beneficiario.id', 101);
        $response->assertJsonPath('data.1.alumno_beneficiario.nombre', 'Alumno 2');
        $response->assertJsonPath('data.1.familia_beneficiaria.id', 2);
        $response->assertJsonPath('data.1.familia_recolectora.id', 10);
        $response->assertJsonPath('data.1.familia_recolectora.banco', 'Banco X');
        $response->assertJsonPath('data.1.familia_recolectora.numero_cuenta', '123');
        $response->assertJsonPath('data.1.familia_recolectora.tipo_cuenta', 'Caja de ahorro');
        $response->assertJsonPath('data.1.familia_recolectora.nombre_cuenta', 'Titular');
        $response->assertJsonPath('data.1.familia_recolectora.moneda', 'ARS');
        $response->assertJsonPath('data.1.aportaron_count', 2);
        $response->assertJsonPath('data.1.total_count', 10);
    }
}

