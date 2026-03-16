<?php

namespace App\Jobs;

use App\Domain\Services\NotificacionMeriendaManager;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnviarRecordatoriosMerienda implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificacionMeriendaManager $manager): void
    {
        $hoy = Carbon::today();
        $fechaObjetivo = $hoy->copy()->addDay();
        if ($hoy->isFriday()) {
            $fechaObjetivo = $hoy->copy()->addDays(3);
        }

        $manager->prepararParaFecha($fechaObjetivo);
        $manager->enviarPendientesParaFecha($fechaObjetivo, 2);
    }
}
