<?php

use App\Jobs\EnviarRecordatoriosMerienda;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(EnviarRecordatoriosMerienda::class)
    ->twiceDaily(8, 20)
    ->name('recordatorios-merienda');

Schedule::command('meriendas:generar-mes-siguiente')
    ->monthlyOn(config('meriendas.asignacion.dia_recalculo_mes_siguiente', 25), '00:00')
    ->name('generar-asignaciones-mes-siguiente');

Schedule::command('meriendas:limpiar-notificaciones')
    ->weeklyOn(1, '03:00')
    ->name('limpiar-notificaciones-merienda');
