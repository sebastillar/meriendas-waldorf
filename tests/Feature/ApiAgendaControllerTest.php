<?php

namespace Tests\Feature;

use App\Domain\Models\ConfiguracionCalendario;
use App\Domain\Models\DiaSinClase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAgendaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ConfiguracionCalendario::factory()->create([
            'anio' => 2026,
            'fecha_inicio_clases' => '2026-03-03',
            'fecha_fin_clases' => '2026-12-20',
        ]);
    }

    public function test_semana_endpoint_returns_json_structure()
    {
        $response = $this->getJson('/api/agenda/semana');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'fecha',
                    'dia',
                    'cereal',
                    'fruta',
                    'elaboracion',
                    'es_feriado',
                ],
            ],
        ]);
    }

    public function test_mes_endpoint_requires_anio_and_mes()
    {
        $response = $this->getJson('/api/agenda/mes');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['anio', 'mes']);
    }

    public function test_calendario_endpoint_returns_configuracion()
    {
        DiaSinClase::factory()->create([
            'fecha' => '2026-03-24',
            'motivo' => 'Feriado de prueba',
        ]);

        $response = $this->getJson('/api/calendario/2026');

        $response->assertOk();
        $response->assertJsonFragment([
            'anio' => 2026,
            'fecha_inicio_clases' => '2026-03-03',
            'fecha_fin_clases' => '2026-12-20',
        ]);
        $response->assertJsonStructure([
            'data' => [
                'anio',
                'fecha_inicio_clases',
                'fecha_fin_clases',
                'dias_sin_clase' => [
                    '*' => ['fecha', 'motivo'],
                ],
            ],
        ]);
    }
}

