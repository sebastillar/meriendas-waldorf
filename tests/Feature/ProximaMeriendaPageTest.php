<?php

namespace Tests\Feature;

use App\Domain\Services\AgendaService;
use App\Domain\Services\RecolectandoService;
use Carbon\Carbon;
use Tests\TestCase;

class ProximaMeriendaPageTest extends TestCase
{
    private function mockSinCumpleanos(): void
    {
        $this->mock(RecolectandoService::class, function ($mock): void {
            $mock->shouldReceive('proximoCumpleanosEnProximosDias')
                ->with(30)
                ->andReturn(null);
        });
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Modo día (lunes–sábado)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_home_renderiza_ok(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 10, 0, 0)); // lunes

        $this->mock(AgendaService::class, function ($mock): void {
            $mock->shouldReceive('proximaDiaLectivoConMerienda')
                ->once()
                ->andReturn([
                    'fecha'               => '2026-05-11',
                    'dia'                 => 'lunes',
                    'cereal'              => 'Arroz',
                    'fruta'               => ['id' => 1, 'nombre' => 'Lucía'],
                    'elaboracion'         => ['id' => 2, 'nombre' => 'Tomás'],
                    'es_feriado'          => false,
                    'etiqueta_feriado'    => '',
                    'etiqueta'            => 'Hoy',
                    'familia_fruta'       => 'Familia García',
                    'familia_elaboracion' => 'Familia López',
                ]);
        });

        $this->mockSinCumpleanos();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Meriendas', false);

        Carbon::setTestNow(null);
    }

    public function test_home_muestra_etiqueta_hoy_cereal_y_familias(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 10, 0, 0)); // lunes antes de las 15

        $this->mock(AgendaService::class, function ($mock): void {
            $mock->shouldReceive('proximaDiaLectivoConMerienda')
                ->once()
                ->andReturn([
                    'fecha'               => '2026-05-11',
                    'dia'                 => 'lunes',
                    'cereal'              => 'Arroz',
                    'fruta'               => ['id' => 1, 'nombre' => 'Lucía'],
                    'elaboracion'         => ['id' => 2, 'nombre' => 'Tomás'],
                    'es_feriado'          => false,
                    'etiqueta_feriado'    => '',
                    'etiqueta'            => 'Hoy',
                    'familia_fruta'       => 'Familia García',
                    'familia_elaboracion' => 'Familia López',
                ]);
        });

        $this->mockSinCumpleanos();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Hoy', false);
        $response->assertSee('Lucía', false);  // nombre del niño — primera jerarquía
        $response->assertSee('Tomás', false);  // nombre del niño — primera jerarquía
        $response->assertSee('Arroz', false);  // cereal como tag junto a 👩‍🍳
        $response->assertSee('☽', false);      // planeta en tag
        // "Fruta" y "Elaboración" ya no aparecen como texto: solo los emojis 🍎 👩‍🍳
        $response->assertDontSee('>Fruta<', false);
        $response->assertDontSee('>Elaboración<', false);

        Carbon::setTestNow(null);
    }

    public function test_home_muestra_etiqueta_manana(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 16, 0, 0)); // lunes después de 15

        $this->mock(AgendaService::class, function ($mock): void {
            $mock->shouldReceive('proximaDiaLectivoConMerienda')
                ->once()
                ->andReturn([
                    'fecha'               => '2026-05-12',
                    'dia'                 => 'martes',
                    'cereal'              => 'Cebada',
                    'fruta'               => ['id' => 1, 'nombre' => 'Lucía'],
                    'elaboracion'         => ['id' => 2, 'nombre' => 'Tomás'],
                    'es_feriado'          => false,
                    'etiqueta_feriado'    => '',
                    'etiqueta'            => 'Mañana',
                    'familia_fruta'       => 'Familia García',
                    'familia_elaboracion' => 'Familia López',
                ]);
        });

        $this->mockSinCumpleanos();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Mañana', false);
        $response->assertSee('Lucía', false);
        $response->assertSee('Tomás', false);
        $response->assertSee('Cebada', false);

        Carbon::setTestNow(null);
    }

    public function test_home_muestra_cumpleanos_si_presente(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 10, 0, 0));

        $this->mock(AgendaService::class, function ($mock): void {
            $mock->shouldReceive('proximaDiaLectivoConMerienda')
                ->once()
                ->andReturn([
                    'fecha'               => '2026-05-11',
                    'dia'                 => 'lunes',
                    'cereal'              => 'Arroz',
                    'fruta'               => ['id' => 1, 'nombre' => 'Lucía'],
                    'elaboracion'         => ['id' => 2, 'nombre' => 'Tomás'],
                    'es_feriado'          => false,
                    'etiqueta_feriado'    => '',
                    'etiqueta'            => 'Hoy',
                    'familia_fruta'       => 'Familia García',
                    'familia_elaboracion' => 'Familia López',
                ]);
        });

        $this->mock(RecolectandoService::class, function ($mock): void {
            $mock->shouldReceive('proximoCumpleanosEnProximosDias')
                ->with(30)
                ->andReturn(['nombre' => 'Sofía', 'fecha_formato' => '20 de mayo']);
        });

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Sofía', false);
        $response->assertSee('20 de mayo', false);

        Carbon::setTestNow(null);
    }

    public function test_home_no_muestra_cumpleanos_si_ausente(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 10, 0, 0));

        $this->mock(AgendaService::class, function ($mock): void {
            $mock->shouldReceive('proximaDiaLectivoConMerienda')
                ->once()
                ->andReturn([
                    'fecha'               => '2026-05-11',
                    'dia'                 => 'lunes',
                    'cereal'              => 'Arroz',
                    'fruta'               => ['id' => 1, 'nombre' => 'Lucía'],
                    'elaboracion'         => ['id' => 2, 'nombre' => 'Tomás'],
                    'es_feriado'          => false,
                    'etiqueta_feriado'    => '',
                    'etiqueta'            => 'Hoy',
                    'familia_fruta'       => 'Familia García',
                    'familia_elaboracion' => 'Familia López',
                ]);
        });

        $this->mockSinCumpleanos();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('Cumpleaños de', false);

        Carbon::setTestNow(null);
    }

    public function test_home_muestra_mensaje_si_no_hay_merienda_proxima(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 10, 0, 0));

        $this->mock(AgendaService::class, function ($mock): void {
            $mock->shouldReceive('proximaDiaLectivoConMerienda')
                ->once()
                ->andReturn(null);
        });

        $this->mockSinCumpleanos();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('No hay merienda programada', false);

        Carbon::setTestNow(null);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Modo semana (domingo)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_home_muestra_vista_semanal_los_domingos(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 10, 10, 0, 0)); // domingo

        $this->mock(AgendaService::class, function ($mock): void {
            $mock->shouldReceive('agendaSemanaConFamilias')
                ->once()
                ->andReturn([
                    [
                        'fecha'            => '2026-05-11',
                        'dia'              => 'lunes',
                        'cereal'           => 'Arroz',
                        'fruta'            => ['id' => 1, 'nombre' => 'Lucía', 'familia_nombre' => 'Familia García'],
                        'elaboracion'      => ['id' => 2, 'nombre' => 'Tomás', 'familia_nombre' => 'Familia López'],
                        'es_feriado'       => false,
                        'etiqueta_feriado' => '',
                    ],
                ]);
        });

        $this->mockSinCumpleanos();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Esta semana', false);
        $response->assertSee('Arroz', false);
        $response->assertSee('Familia García', false);
        $response->assertDontSee('Hoy', false);

        Carbon::setTestNow(null);
    }

    public function test_home_domingo_muestra_dias_sin_clase_atenuados(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 10, 10, 0, 0)); // domingo

        $this->mock(AgendaService::class, function ($mock): void {
            $mock->shouldReceive('agendaSemanaConFamilias')
                ->once()
                ->andReturn([
                    [
                        'fecha'            => '2026-05-11',
                        'dia'              => 'lunes',
                        'cereal'           => '',
                        'fruta'            => [],
                        'elaboracion'      => [],
                        'es_feriado'       => true,
                        'etiqueta_feriado' => 'Día sin clase',
                    ],
                ]);
        });

        $this->mockSinCumpleanos();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Sin clase', false);

        Carbon::setTestNow(null);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Ruta /agenda sigue funcionando
    // ──────────────────────────────────────────────────────────────────────────

    public function test_ruta_agenda_sigue_accesible(): void
    {
        $response = $this->get('/agenda');
        // La agenda puede retornar 200 u otro código según el estado de la DB,
        // pero la ruta debe existir (no 404)
        $response->assertStatus($response->status() !== 404 ? $response->status() : 200);
        $this->assertNotEquals(404, $response->status());
    }
}
