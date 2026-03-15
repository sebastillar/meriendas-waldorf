<?php

use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\AsignacionIntercambioController;
use App\Http\Controllers\Api\EstadisticasController;
use Illuminate\Support\Facades\Route;

Route::get('/agenda/semana', [AgendaController::class, 'semana']);
Route::get('/agenda/semana.csv', [AgendaController::class, 'semanaCsv']);
Route::get('/agenda/mes', [AgendaController::class, 'mes']);
Route::get('/agenda/mes.csv', [AgendaController::class, 'mesCsv']);
Route::get('/estadisticas/resumen', [EstadisticasController::class, 'resumen']);
Route::post('/asignaciones/{asignacion}/intercambiar', [AsignacionIntercambioController::class, 'intercambiar']);
