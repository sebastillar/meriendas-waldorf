<?php

namespace Tests\Feature;

use Tests\TestCase;

class ColoresPorDiaPageTest extends TestCase
{
    public function test_colores_por_dia_page_ok(): void
    {
        $response = $this->get('/colores-por-dia');

        $response->assertOk();
        $response->assertSee('Ritmo de la semana', false);
        $response->assertSee('Lunes', false);
        $response->assertSee('Azul', false);
        $response->assertSee('Cereal: Arroz', false);
        $response->assertSee('Planeta: Luna', false);
        $response->assertSee('Viernes', false);
        $response->assertSee('Verde', false);
    }
}
