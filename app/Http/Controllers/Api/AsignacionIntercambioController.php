<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\IntercambioService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IntercambiarAsignacionRequest;
use App\Http\Transformers\AsignacionTransformer;
use Illuminate\Http\JsonResponse;

/**
 * @group Asignaciones
 *
 * Intercambio de la persona asignada (fruta o elaboración) en una fecha.
 */
class AsignacionIntercambioController extends Controller
{
    public function __construct(
        private IntercambioService $intercambioService
    ) {}

    /**
     * Intercambiar asignación
     *
     * Cambia el alumno asignado para un rol (fruta o elaboración) en la asignación indicada.
     * Body: `rol` (fruta|elaboracion), `alumno_nuevo_id`, `motivo` (opcional).
     */
    public function intercambiar(IntercambiarAsignacionRequest $request, int $asignacion): JsonResponse
    {
        try {
            $asignacionModel = $this->intercambioService->intercambiar(
                $asignacion,
                $request->validated('rol'),
                $request->validated('alumno_nuevo_id'),
                $request->validated('motivo')
            );

            return response()->json([
                'data' => fractal()->item($asignacionModel, new AsignacionTransformer())->toArray()['data'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
