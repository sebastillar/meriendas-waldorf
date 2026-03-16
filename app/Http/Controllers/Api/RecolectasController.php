<?php

namespace App\Http\Controllers\Api;

use App\Domain\Models\Familia;
use App\Domain\Services\RecolectandoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @group Recolectas
 *
 * Endpoints para consultar el estado de colectas de cumpleaños.
 */
class RecolectasController extends Controller
{
    public function __construct(
        private RecolectandoService $recolectandoService
    ) {}

    /**
     * Colectas del mes actual
     *
     * Devuelve la lista de colectas cuyo cumpleaños ocurre en el mes actual, con su estado y progreso.
     */
    public function mesActual(): JsonResponse
    {
        $data = $this->recolectandoService->recolectasDelMesActual();

        return response()->json([
            'data' => array_map(function (array $row): array {
                /** @var \App\Domain\Models\Alumno $alumno */
                $alumno = $row['alumno_beneficiario'];
                /** @var \App\Domain\Models\Familia $beneficiaria */
                $beneficiaria = $row['familia_beneficiaria'];
                /** @var \App\Domain\Models\Familia|null $recolectora */
                $recolectora = $row['familia_recolectora'];

                return [
                    'estado' => $row['estado'],
                    'fecha_cumpleanos' => $row['fecha_cumpleanos']->toDateString(),
                    'alumno_beneficiario' => [
                        'id' => (int) $alumno->id,
                        'nombre' => $alumno->nombre,
                    ],
                    'familia_beneficiaria' => [
                        'id' => (int) $beneficiaria->id,
                        'nombre' => $beneficiaria->nombre_para_listado,
                    ],
                    'familia_recolectora' => $recolectora ? $this->familiaRecolectoraToArray($recolectora) : null,
                    'aportaron_count' => (int) $row['aportaron_count'],
                    'total_count' => (int) $row['total_count'],
                ];
            }, $data),
        ]);
    }

    private function familiaRecolectoraToArray(Familia $familia): array
    {
        return [
            'id' => (int) $familia->id,
            'nombre' => $familia->nombre_para_listado,
            'banco' => $familia->banco,
            'numero_cuenta' => $familia->numero_cuenta,
            'tipo_cuenta' => $familia->tipo_cuenta,
            'nombre_cuenta' => $familia->nombre_cuenta,
            'moneda' => $familia->moneda,
            'monto_aportar' => (float) config('meriendas.regalo.monto_aportar', 300),
        ];
    }
}

