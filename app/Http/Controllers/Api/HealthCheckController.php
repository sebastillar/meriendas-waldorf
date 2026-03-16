<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Sistema
 *
 * Endpoints de diagnóstico y estado de la API.
 */
class HealthCheckController extends Controller
{
    /**
     * Healthcheck de la API
     *
     * Permite verificar rápidamente que la API y la base de datos están disponibles.
     * Devuelve `200 OK` junto con información mínima de estado.
     */
    public function __invoke(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
            $databaseStatus = 'ok';
        } catch (\Throwable $e) {
            $databaseStatus = 'error';
        }

        return response()->json([
            'status' => 'ok',
            'database' => $databaseStatus,
        ]);
    }
}

