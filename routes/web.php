<?php

use App\Http\Controllers\AgendaPublicController;
use App\Http\Controllers\ColoresPorDiaController;
use App\Http\Controllers\CumpleanosController;
use App\Http\Controllers\ResumenMensualController;
use App\Http\Controllers\SobreAlgoritmoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AgendaPublicController::class, 'index'])->name('agenda.public');
Route::get('/resumen-mensual', [ResumenMensualController::class, 'index'])->name('resumen.mensual');
Route::get('/cumpleanos', [CumpleanosController::class, 'index'])->name('cumpleanos.index');
Route::get('/sobre-algoritmo', [SobreAlgoritmoController::class, 'index'])->name('sobre.algoritmo');
Route::get('/colores-por-dia', [ColoresPorDiaController::class, 'index'])->name('colores.por.dia');
Route::get('/agenda/descargar/csv', [AgendaPublicController::class, 'descargarCsv'])->name('agenda.descargar.csv');
Route::get('/agenda/descargar/excel', [AgendaPublicController::class, 'descargarExcel'])->name('agenda.descargar.excel');
Route::get('/agenda/descargar/pdf', [AgendaPublicController::class, 'descargarPdf'])->name('agenda.descargar.pdf');
Route::get('/agenda/imprimir', [AgendaPublicController::class, 'imprimir'])->name('agenda.imprimir');
Route::get('/agenda/descargar/ical', [AgendaPublicController::class, 'descargarIcal'])->name('agenda.descargar.ical');
Route::get('/agenda/dia/{fecha}.ical', [AgendaPublicController::class, 'icalUnDia'])->name('agenda.dia.ical')->where('fecha', '[0-9]{4}-[0-9]{2}-[0-9]{2}');
