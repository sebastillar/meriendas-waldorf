<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\EstadisticasService;
use App\Http\Controllers\Controller;
use App\Http\Transformers\EstadisticaAlumnoTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Estadísticas
 *
 * Resumen de veces que cada alumno llevó fruta o elaboración, opcionalmente filtrado por año y/o mes.
 */
class EstadisticasController extends Controller
{
    public function __construct(
        private EstadisticasService $estadisticasService
    ) {}

    /**
     * Resumen por alumno
     *
     * Devuelve estadísticas (veces fruta, veces elaboración) por alumno. Opcional: `anio`, `mes`.
     */
    public function resumen(Request $request): JsonResponse
    {
        $anio = $request->filled('anio') ? (int) $request->input('anio') : null;
        $mes = $request->filled('mes') ? (int) $request->input('mes') : null;
        $filas = $this->estadisticasService->resumenPorAlumno($anio, $mes);

        return response()->json([
            'data' => fractal()->collection($filas, new EstadisticaAlumnoTransformer())->toArray()['data'],
        ]);
    }
}
