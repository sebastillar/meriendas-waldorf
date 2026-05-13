<?php

namespace App\Http\Controllers;

use App\Domain\Services\AgendaService;
use App\Domain\Services\RecolectandoService;
use App\Support\ColoresDiaWaldorf;
use Carbon\Carbon;
use Illuminate\View\View;

class ProximaMeriendaController extends Controller
{
    public function __construct(
        private AgendaService $agendaService,
        private RecolectandoService $recolectandoService,
    ) {}

    public function index(): View
    {
        $hoy = Carbon::today();
        $proximoCumpleanos = $this->recolectandoService->proximoCumpleanosEnProximosDias(30);

        if ($hoy->isSunday()) {
            return $this->vistaModoDomingo($proximoCumpleanos);
        }

        return $this->vistaModoDia($proximoCumpleanos);
    }

    private function vistaModoDia(?array $proximoCumpleanos): View
    {
        $proximaMerienda = $this->agendaService->proximaDiaLectivoConMerienda();

        $colorDia = null;
        $bgTintCss = 'rgba(249,250,251,1)';

        if ($proximaMerienda) {
            $dow = Carbon::parse($proximaMerienda['fecha'])->dayOfWeek;
            $colorDia = ColoresDiaWaldorf::infoPorDiaSemana($dow);
            $bgTintCss = $colorDia['bg_tint_css'] ?? $bgTintCss;
        }

        return view('proxima-merienda.index', [
            'modo'             => 'dia',
            'proximaMerienda'  => $proximaMerienda,
            'colorDia'         => $colorDia,
            'bgTintCss'        => $bgTintCss,
            'proximoCumpleanos' => $proximoCumpleanos,
        ]);
    }

    private function vistaModoDomingo(?array $proximoCumpleanos): View
    {
        $inicioProximaSemana = Carbon::tomorrow()->startOfWeek();
        $filasProximaSemana = $this->agendaService->agendaSemanaConFamilias($inicioProximaSemana);
        $diasInfo = ColoresDiaWaldorf::diasSemana();

        return view('proxima-merienda.index', [
            'modo'               => 'semana',
            'filasProximaSemana' => $filasProximaSemana,
            'diasInfo'           => $diasInfo,
            'bgTintCss'          => 'rgba(147,112,219,0.07)',
            'proximoCumpleanos'  => $proximoCumpleanos,
        ]);
    }
}
