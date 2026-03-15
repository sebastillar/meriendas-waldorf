<?php

namespace App\Domain\Services;

use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\FamiliaRepositoryInterface;
use App\Domain\Repositories\RecolectaAporteRepositoryInterface;

/**
 * Lógica de aportes a la recolecta para regalo (quién ya aportó por familia beneficiaria).
 */
class RecolectaAportesService
{
    public function __construct(
        private RecolectaAporteRepositoryInterface $recolectaAporteRepository,
        private AlumnoRepositoryInterface $alumnoRepository,
        private FamiliaRepositoryInterface $familiaRepository
    ) {}

    /**
     * Indica si la familia dada es beneficiaria de alguna recolecta (alguna familia "Regala a" esta).
     */
    public function esBeneficiaria(int $familiaId): bool
    {
        return $this->familiaRepository->activas()
            ->contains(fn ($f) => (int) $f->familia_regalo_id === $familiaId);
    }

    /**
     * Alumnos que pueden aportar (activos, de otras familias) para el select/checkboxes.
     * @return array<int, string> id => nombre
     */
    public function alumnosParaAportes(int $familiaBeneficiariaId): array
    {
        $alumnos = $this->alumnoRepository->activos()
            ->filter(fn ($a) => (int) $a->familia_id !== $familiaBeneficiariaId);
        return $alumnos->mapWithKeys(fn ($a) => [$a->id => $a->nombre])->all();
    }

    /**
     * IDs de alumnos que ya aportaron para esta familia beneficiaria.
     * @return array<int>
     */
    public function getAlumnoIdsQueAportaron(int $familiaBeneficiariaId): array
    {
        return $this->recolectaAporteRepository->getAlumnoIdsQueAportaron($familiaBeneficiariaId);
    }

    public function syncAportes(int $familiaBeneficiariaId, array $alumnoIds): void
    {
        $this->recolectaAporteRepository->syncAportes($familiaBeneficiariaId, $alumnoIds);
    }
}
