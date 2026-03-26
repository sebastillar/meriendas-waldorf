<?php

namespace App\Http\Controllers;

use App\Domain\Services\RecolectandoService;
use Carbon\Carbon;
use Illuminate\View\View;

class CumpleanosController extends Controller
{
    public function __construct(
        private RecolectandoService $recolectandoService
    ) {}

    public function index(): View
    {
        $recolectaActual = $this->recolectandoService->recolectaActual();
        $recolectasMes = $this->recolectandoService->recolectasDelMesActual();

        $filas = array_map(function (array $item): array {
            return [
                'nino' => (string) $item['alumno_beneficiario']->nombre,
                'fecha_cumpleanos' => $item['fecha_cumpleanos']->copy()->locale('es')->isoFormat('DD/MM/YYYY'),
                'familia_encargada' => $item['familia_recolectora']?->nombre_para_listado ?? 'Sin familia asignada',
            ];
        }, $recolectasMes);

        $titulo = Carbon::today()->locale('es')->isoFormat('MMMM [de] YYYY');

        return view('cumpleanos.index', [
            'titulo' => $titulo,
            'filas' => $filas,
            'recolectaActual' => $recolectaActual,
        ]);
    }
}
