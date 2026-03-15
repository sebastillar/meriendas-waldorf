<?php

namespace App\Console\Commands;

use App\Domain\Services\GeneradorAgendaService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerarAsignacionesMesSiguienteCommand extends Command
{
    protected $signature = 'meriendas:generar-mes-siguiente';

    protected $description = 'Genera o recalcula las asignaciones del mes siguiente. Se ejecuta el día configurado de cada mes (por defecto el 25).';

    public function handle(GeneradorAgendaService $generador): int
    {
        $hoy = Carbon::today();
        $diaRecalculo = config('meriendas.asignacion.dia_recalculo_mes_siguiente', 25);

        if ($hoy->day !== $diaRecalculo) {
            return self::SUCCESS;
        }

        $proximoMes = $hoy->copy()->addMonth();
        $anio = (int) $proximoMes->year;
        $mes = (int) $proximoMes->month;

        $n = $generador->generarParaMes($anio, $mes);
        $this->info("Se generaron {$n} asignaciones para {$anio}-{$mes} (mes siguiente).");

        return self::SUCCESS;
    }
}
