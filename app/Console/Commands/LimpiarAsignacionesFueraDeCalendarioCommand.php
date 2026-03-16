<?php

namespace App\Console\Commands;

use App\Domain\Models\Asignacion;
use App\Domain\Models\ConfiguracionCalendario;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarAsignacionesFueraDeCalendarioCommand extends Command
{
    protected $signature = 'meriendas:limpiar-fuera-de-calendario {--anio= : Limpiar solo un año (ej. 2026)}';

    protected $description = 'Elimina asignaciones fuera del rango [inicio_clases, fin_clases] definido en el calendario escolar.';

    public function handle(): int
    {
        $anioOpt = $this->option('anio');
        $anio = is_string($anioOpt) && ctype_digit($anioOpt) ? (int) $anioOpt : null;

        $configs = ConfiguracionCalendario::query()
            ->when($anio !== null, fn ($q) => $q->where('anio', $anio))
            ->get();

        if ($configs->isEmpty()) {
            $this->info('No hay configuraciones de calendario para limpiar.');
            return self::SUCCESS;
        }

        $driver = DB::connection()->getDriverName();

        foreach ($configs as $config) {
            $inicio = $config->fecha_inicio_clases?->toDateString();
            if (!$inicio) {
                continue;
            }

            $fin = $config->fecha_fin_clases?->toDateString();

            $this->line("Año {$config->anio}: inicio={$inicio}" . ($fin ? " fin={$fin}" : ' fin=(sin definir)'));

            $query = Asignacion::query();

            if ($driver === 'pgsql') {
                $query->whereRaw('EXTRACT(YEAR FROM fecha) = ?', [$config->anio]);
            } else {
                $query->whereYear('fecha', $config->anio);
            }

            $query->where(function ($q) use ($inicio, $fin) {
                $q->where('fecha', '<', $inicio);
                if ($fin) {
                    $q->orWhere('fecha', '>', $fin);
                }
            });

            $count = (clone $query)->count();
            if ($count === 0) {
                $this->info(' - No hay asignaciones fuera de rango.');
                continue;
            }

            $this->warn(" - Eliminando {$count} asignaciones fuera de rango...");
            $deleted = $query->delete();
            $this->info(" - Eliminadas: {$deleted}");
        }

        $this->info('Limpieza finalizada.');
        return self::SUCCESS;
    }
}

