<?php

namespace App\Console\Commands;

use App\Domain\Services\GeneradorAgendaService;
use Illuminate\Console\Command;

class GenerarAsignacionesMesCommand extends Command
{
    protected $signature = 'meriendas:generar-mes {anio : Año (ej. 2026)} {mes : Mes (1-12)}';

    protected $description = 'Genera asignaciones de fruta y elaboración para un mes dado.';

    public function handle(GeneradorAgendaService $generador): int
    {
        $anio = (int) $this->argument('anio');
        $mes = (int) $this->argument('mes');

        if ($mes < 1 || $mes > 12) {
            $this->error('El mes debe estar entre 1 y 12.');
            return self::FAILURE;
        }

        $n = $generador->generarParaMes($anio, $mes);
        $this->info("Se generaron {$n} asignaciones para {$anio}-{$mes}.");

        return self::SUCCESS;
    }
}
