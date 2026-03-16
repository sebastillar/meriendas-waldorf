<?php

namespace Tests\Feature;

use App\Domain\Models\Alumno;
use App\Domain\Models\Familia;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\FamiliaRepositoryInterface;
use App\Domain\Services\RecolectaAportesService;
use App\Domain\Services\RecolectandoService;
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
}

