<?php

use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\AsignacionIntercambioController;
use App\Http\Controllers\Api\CalendarioController;
use App\Http\Controllers\Api\EstadisticasController;
use App\Http\Controllers\Api\HealthCheckController;
use App\Http\Controllers\Api\RecolectasController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthCheckController::class);

Route::get('/agenda/semana', [AgendaController::class, 'semana']);
Route::get('/agenda/semana.csv', [AgendaController::class, 'semanaCsv']);
Route::get('/agenda/mes', [AgendaController::class, 'mes']);
Route::get('/agenda/mes.csv', [AgendaController::class, 'mesCsv']);
Route::get('/estadisticas/resumen', [EstadisticasController::class, 'resumen']);
Route::post('/asignaciones/{asignacion}/intercambiar', [AsignacionIntercambioController::class, 'intercambiar']);
Route::get('/calendario/{anio}', [CalendarioController::class, 'show']);
Route::get('/recolectas/cumpleanos/mes-actual', [RecolectasController::class, 'mesActual']);
