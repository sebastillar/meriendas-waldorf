<?php

namespace App\Domain\Services;

use App\Domain\Models\Familia;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\FamiliaRepositoryInterface;
use Carbon\Carbon;

class FamiliaService
{
    public function __construct(
        private FamiliaRepositoryInterface $familiaRepository,
        private AlumnoRepositoryInterface $alumnoRepository,
        private RecalculoAsignacionesService $recalculoAsignacionesService
    ) {}

    public function find(int $id): ?Familia
    {
        return $this->familiaRepository->find($id);
    }

    /** @return \Illuminate\Support\Collection<int, Familia> */
    public function todos(): \Illuminate\Support\Collection
    {
        return $this->familiaRepository->todos();
    }

    /** @return \Illuminate\Support\Collection<int, Familia> */
    public function activas(): \Illuminate\Support\Collection
    {
        return $this->familiaRepository->activas();
    }

    public function crear(array $data): Familia
    {
        $familia = new Familia($data);
        return $this->familiaRepository->guardar($familia);
    }

    public function actualizar(int $id, array $data): Familia
    {
        $familia = $this->familiaRepository->find($id);
        if (!$familia) {
            throw new \InvalidArgumentException('Familia no encontrada.');
        }
        if (isset($data['familia_regalo_id']) && (int) $data['familia_regalo_id'] === $id) {
            throw new \InvalidArgumentException('Una familia no puede asignarse a sí misma como familia para regalo.');
        }
        $familia->fill($data);
        return $this->familiaRepository->guardar($familia);
    }

    public function darDeBaja(int $id): Familia
    {
        $familia = $this->familiaRepository->find($id);
        if (!$familia) {
            throw new \InvalidArgumentException('Familia no encontrada.');
        }
        if (!$familia->activo) {
            return $familia;
        }

        $familia->activo = false;
        $this->familiaRepository->guardar($familia);

        foreach ($familia->alumnos as $alumno) {
            $alumno->activo = false;
            $this->alumnoRepository->guardar($alumno);
        }

        $this->recalculoAsignacionesService->recalcularFuturasDesde(Carbon::today());

        return $this->familiaRepository->find($id);
    }

    public function darDeAlta(int $id): Familia
    {
        $familia = $this->familiaRepository->find($id);
        if (!$familia) {
            throw new \InvalidArgumentException('Familia no encontrada.');
        }
        if ($familia->activo) {
            return $familia;
        }

        $familia->activo = true;
        $this->familiaRepository->guardar($familia);

        foreach ($familia->alumnos as $alumno) {
            $alumno->activo = true;
            $this->alumnoRepository->guardar($alumno);
        }

        $this->recalculoAsignacionesService->recalcularFuturasDesde(Carbon::today());

        return $this->familiaRepository->find($id);
    }

    public function eliminar(int $id): bool
    {
        $familia = $this->familiaRepository->find($id);
        if (!$familia) {
            return false;
        }
        return $familia->delete();
    }
}
