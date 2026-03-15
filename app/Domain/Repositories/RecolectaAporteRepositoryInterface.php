<?php

namespace App\Domain\Repositories;

interface RecolectaAporteRepositoryInterface
{
    /**
     * IDs de alumnos que ya aportaron para esta familia beneficiaria.
     * @return array<int>
     */
    public function getAlumnoIdsQueAportaron(int $familiaBeneficiariaId): array;

    /**
     * Sincroniza la lista de alumnos que aportaron (reemplaza la anterior).
     */
    public function syncAportes(int $familiaBeneficiariaId, array $alumnoIds): void;
}
