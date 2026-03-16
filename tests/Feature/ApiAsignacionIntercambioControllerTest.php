<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAsignacionIntercambioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_intercambiar_returns_422_for_invalid_payload()
    {
        // Usamos un ID cualquiera: la validación del request ocurre antes de la lógica de intercambio.
        $response = $this->postJson('/api/asignaciones/123/intercambiar', [
            // payload vacío -> debería fallar validación
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rol', 'alumno_nuevo_id']);
    }
}

