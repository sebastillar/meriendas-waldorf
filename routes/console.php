<?php

use App\Jobs\EnviarRecordatoriosMerienda;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(EnviarRecordatoriosMerienda::class)
    ->dailyAt(config('recordatorio.hora', '18:00'))
    ->name('recordatorios-merienda');

Schedule::command('meriendas:generar-mes-siguiente')
    ->monthlyOn(config('meriendas.asignacion.dia_recalculo_mes_siguiente', 25), '00:00')
    ->name('generar-asignaciones-mes-siguiente');
