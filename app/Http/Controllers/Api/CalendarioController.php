<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\ConfiguracionCalendario;
use App\Domain\Models\DiaSinClase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Calendario escolar
 *
 * Endpoints para obtener configuración de calendario (inicio/fin de clases y días sin clase).
 */
class CalendarioController extends Controller
{
    /**
     * Configuración de calendario por año
     *
     * Devuelve `fecha_inicio_clases`, `fecha_fin_clases` y los `dias_sin_clase` del año indicado.
     */
    public function show(Request $request, int $anio): JsonResponse
    {
        $config = ConfiguracionCalendario::where('anio', $anio)->first();

        $desde = "{$anio}-01-01";
        $hasta = "{$anio}-12-31";
        $diasSinClase = DiaSinClase::whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->get(['fecha', 'motivo'])
            ->map(fn (DiaSinClase $d) => [
                'fecha' => $d->fecha?->toDateString(),
                'motivo' => $d->motivo,
            ])
            ->values()
            ->all();

        return response()->json([
            'data' => [
                'anio' => $anio,
                'fecha_inicio_clases' => $config?->fecha_inicio_clases?->toDateString(),
                'fecha_fin_clases' => $config?->fecha_fin_clases?->toDateString(),
                'dias_sin_clase' => $diasSinClase,
            ],
        ]);
    }
}

