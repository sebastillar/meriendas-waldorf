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
        $recolectasMes = $this->recolectandoService->recolectasDelMesActual();
        $colectasEnCurso = array_values(array_filter(
            $recolectasMes,
            fn (array $item): bool => ($item['estado'] ?? '') === 'activa'
        ));

        $cumpleanosAnuales = $this->recolectandoService->cumpleanosConFamiliaEncargada();
        $filas = array_map(function (array $item): array {
            return [
                'nino' => (string) $item['alumno']->nombre,
                'fecha_cumpleanos' => $item['fecha_cumpleanos']->copy()->locale('es')->isoFormat('DD/MM'),
                'familia_encargada' => $item['familia_encargada']?->nombre_para_listado ?? 'Sin familia asignada',
            ];
        }, $cumpleanosAnuales);
        $titulo = Carbon::today()->locale('es')->isoFormat('YYYY');

        return view('cumpleanos.index', [
            'titulo' => $titulo,
            'filas' => $filas,
            'colectasEnCurso' => $colectasEnCurso,
        ]);
    }
}
