<?php

namespace Tests\Feature;

use Tests\TestCase;

class ColoresPorDiaPageTest extends TestCase
{
    public function test_colores_por_dia_page_ok(): void
    {
        $response = $this->get('/colores-por-dia');

        $response->assertOk();
        $response->assertSee('Colores por día', false);
        $response->assertSee('Lunes', false);
        $response->assertSee('Azul', false);
        $response->assertSee('Viernes', false);
        $response->assertSee('Verde', false);
    }
}
