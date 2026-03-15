<?php

namespace App\Domain\Repositories\Eloquent;

use App\Domain\Models\RecolectaAporte;
use App\Domain\Repositories\RecolectaAporteRepositoryInterface;
use Illuminate\Support\Facades\Schema;

class RecolectaAporteEloquentRepository implements RecolectaAporteRepositoryInterface
{
    public function getAlumnoIdsQueAportaron(int $familiaBeneficiariaId): array
    {
        if (!Schema::hasTable('recolecta_aportes')) {
            return [];
        }
        return RecolectaAporte::where('familia_beneficiaria_id', $familiaBeneficiariaId)
            ->pluck('alumno_id')
            ->all();
    }

    public function syncAportes(int $familiaBeneficiariaId, array $alumnoIds): void
    {
        if (!Schema::hasTable('recolecta_aportes')) {
            return;
        }
        RecolectaAporte::where('familia_beneficiaria_id', $familiaBeneficiariaId)->delete();
        foreach (array_unique($alumnoIds) as $alumnoId) {
            RecolectaAporte::create([
                'familia_beneficiaria_id' => $familiaBeneficiariaId,
                'alumno_id' => $alumnoId,
            ]);
        }
    }
}
