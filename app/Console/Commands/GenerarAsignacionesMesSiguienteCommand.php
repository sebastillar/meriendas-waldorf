<?php

namespace App\Console\Commands;

use App\Domain\Services\GeneradorAgendaService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerarAsignacionesMesSiguienteCommand extends Command
{
    protected $signature = 'meriendas:generar-mes-siguiente {--force : Fuerza ejecución fuera de la ventana diaria}';

    protected $description = 'Genera asignaciones del mes siguiente desde el día configurado de cada mes (por defecto el 25), evitando regenerar meses ya completos.';

    public function handle(GeneradorAgendaService $generador): int
    {
        $hoy = Carbon::today();
        $diaRecalculo = config('meriendas.asignacion.dia_recalculo_mes_siguiente', 25);
        $forzar = (bool) $this->option('force');

        if (! $forzar && $hoy->day < $diaRecalculo) {
            $this->line("Omitido: hoy es día {$hoy->day}, esperando desde el día {$diaRecalculo}.");
            return self::SUCCESS;
        }

        $proximoMes = $hoy->copy()->addMonth();
        $anio = (int) $proximoMes->year;
        $mes = (int) $proximoMes->month;
        $estadoMes = $generador->estadoCoberturaMes($anio, $mes);

        if ($estadoMes['dias_lectivos_total'] === 0) {
            $this->info("Sin días lectivos para {$anio}-{$mes}; no hay generación pendiente.");
            return self::SUCCESS;
        }

        if ($estadoMes['completo']) {
            $this->info("Mes {$anio}-{$mes} ya completo ({$estadoMes['dias_lectivos_cubiertos']}/{$estadoMes['dias_lectivos_total']} días lectivos).");
            return self::SUCCESS;
        }

        $n = $generador->generarParaMes($anio, $mes);
        $this->info("Se generaron {$n} asignaciones para {$anio}-{$mes} (mes siguiente). Cobertura previa: {$estadoMes['dias_lectivos_cubiertos']}/{$estadoMes['dias_lectivos_total']}.");

        return self::SUCCESS;
    }
}
