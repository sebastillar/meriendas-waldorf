<?php

namespace App\Http\Controllers\Api;

use App\Domain\Services\AgendaService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AgendaMesRequest;
use App\Http\Requests\Api\AgendaSemanaRequest;
use App\Http\Transformers\AgendaDiaTransformer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * @group Agenda
 *
 * Endpoints para consultar la agenda de meriendas por semana o por mes, en JSON o CSV.
 */
class AgendaController extends Controller
{
    public function __construct(
        private AgendaService $agendaService
    ) {}

    /**
     * Agenda de la semana (JSON)
     *
     * Devuelve la agenda de la semana actual o de la semana que comienza en `fecha_inicio`.
     */
    public function semana(AgendaSemanaRequest $request): JsonResponse
    {
        $fechaInicio = $request->filled('fecha_inicio')
            ? Carbon::parse($request->input('fecha_inicio'))
            : null;
        $filas = $this->agendaService->agendaSemana($fechaInicio);

        return response()->json([
            'data' => fractal()->collection($filas, new AgendaDiaTransformer())->toArray()['data'],
        ]);
    }

    /**
     * Agenda de la semana (CSV)
     *
     * Mismos parámetros que semana (JSON). Descarga un CSV con la agenda de la semana.
     */
    public function semanaCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $fechaInicio = $request->filled('fecha_inicio')
            ? Carbon::parse($request->input('fecha_inicio'))
            : null;
        $filas = $this->agendaService->agendaSemana($fechaInicio);

        return $this->csvResponse($filas, 'agenda_semana');
    }

    /**
     * Agenda del mes (JSON)
     *
     * Devuelve la agenda del mes indicado. Requiere `anio` y `mes`.
     */
    public function mes(AgendaMesRequest $request): JsonResponse
    {
        $anio = (int) $request->input('anio');
        $mes = (int) $request->input('mes');
        $filas = $this->agendaService->agendaMes($anio, $mes);

        return response()->json([
            'data' => fractal()->collection($filas, new AgendaDiaTransformer())->toArray()['data'],
        ]);
    }

    /**
     * Agenda del mes (CSV)
     *
     * Mismos parámetros que mes (JSON). Descarga un CSV con la agenda del mes.
     */
    public function mesCsv(AgendaMesRequest $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $anio = (int) $request->input('anio');
        $mes = (int) $request->input('mes');
        $filas = $this->agendaService->agendaMes($anio, $mes);

        return $this->csvResponse($filas, "agenda_mes_{$anio}_{$mes}");
    }

    /**
     * @param array<int, array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool}> $filas
     */
    private function csvResponse(array $filas, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        return Response::streamDownload(function () use ($filas) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($out, ['Fecha', 'Día', 'Cereal', 'Fruta', 'Elaboración', 'Es feriado'], ';');
            foreach ($filas as $row) {
                $nombreFruta = $row['fruta']['nombre'] ?? '';
                $nombreElab = $row['elaboracion']['nombre'] ?? '';
                fputcsv($out, [
                    $row['fecha'],
                    $row['dia'],
                    $row['cereal'],
                    $nombreFruta,
                    $nombreElab,
                    $row['es_feriado'] ? 'Sí' : 'No',
                ], ';');
            }
            fclose($out);
        }, "{$filename}.csv", $headers);
    }
}
