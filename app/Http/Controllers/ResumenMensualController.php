<?php

namespace App\Http\Controllers;

use App\Domain\Services\ResumenMensualService;
use Carbon\Carbon;
use Illuminate\View\View;

class ResumenMensualController extends Controller
{
    public function __construct(
        private ResumenMensualService $resumenMensualService
    ) {}

    public function index(): View
    {
        $hoy = Carbon::today();
        $anio = $hoy->year;
        $mes = $hoy->month;

        $filas = $this->resumenMensualService->resumenParaMes($anio, $mes);
        $diasConMerienda = $this->resumenMensualService->contarDiasConMeriendaEnMes($anio, $mes);

        $nombreMes = $hoy->locale('es')->monthName;
        $titulo = "{$nombreMes} {$anio}";

        return view('resumen-mensual.index', [
            'filas' => $filas,
            'anio' => $anio,
            'mes' => $mes,
            'titulo' => $titulo,
            'diasConMerienda' => $diasConMerienda,
        ]);
    }
}
