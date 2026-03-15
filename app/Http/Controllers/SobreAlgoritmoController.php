<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SobreAlgoritmoController extends Controller
{
    public function index(): View
    {
        return view('sobre-algoritmo.index', [
            'dia_recalculo_asignaciones' => config('meriendas.asignacion.dia_recalculo_mes_siguiente', 25),
        ]);
    }
}
