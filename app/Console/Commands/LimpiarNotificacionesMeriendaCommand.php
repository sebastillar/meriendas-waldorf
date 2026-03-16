<?php

namespace App\Console\Commands;

use App\Domain\Models\NotificacionMerienda;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LimpiarNotificacionesMeriendaCommand extends Command
{
    protected $signature = 'meriendas:limpiar-notificaciones';

    protected $description = 'Elimina registros de notificaciones de merienda de semanas anteriores, dejando solo la semana actual.';

    public function handle(): int
    {
        $inicioSemana = Carbon::today()->startOfWeek(); // lunes

        $borradas = NotificacionMerienda::where('fecha_envio_programada', '<', $inicioSemana->toDateString())
            ->delete();

        $this->info("Registros eliminados: {$borradas}");

        return self::SUCCESS;
    }
}

