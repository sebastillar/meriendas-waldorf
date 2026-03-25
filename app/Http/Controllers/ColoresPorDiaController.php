<?php

namespace App\Http\Controllers;

use App\Support\ColoresDiaWaldorf;
use Illuminate\View\View;

class ColoresPorDiaController extends Controller
{
    public function index(): View
    {
        return view('colores-por-dia.index', [
            'dias' => ColoresDiaWaldorf::diasSemana(),
        ]);
    }
}
