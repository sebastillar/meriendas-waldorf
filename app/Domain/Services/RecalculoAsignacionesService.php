<?php

namespace App\Domain\Services;

use App\Domain\Repositories\AsignacionRepositoryInterface;
use Carbon\Carbon;

class RecalculoAsignacionesService
{
    public function __construct(
        private AsignacionRepositoryInterface $asignacionRepository,
        private GeneradorAgendaService $generadorAgendaService
    ) {}

    /**
     * Elimina asignaciones desde la fecha dada y regenera para cada mes afectado.
     *
     * @param int $mesesAdelante Número de meses a regenerar (por defecto 12)
     */
    public function recalcularFuturasDesde(Carbon $desde, int $mesesAdelante = 12): void
    {
        $desde = $desde->copy()->startOfDay();
        $this->asignacionRepository->eliminarFuturasDesde($desde);

        $cursor = $desde->copy()->startOfMonth();
        $fin = $desde->copy()->addMonths($mesesAdelante);

        while ($cursor->lte($fin)) {
            $this->generadorAgendaService->generarParaMes((int) $cursor->year, (int) $cursor->month);
            $cursor->addMonth();
        }
    }
}
