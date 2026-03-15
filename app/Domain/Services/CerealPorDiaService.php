<?php

namespace App\Domain\Services;

use App\Domain\Models\CerealPorDia;
use App\Domain\Repositories\CerealPorDiaRepositoryInterface;
use Illuminate\Support\Collection;

class CerealPorDiaService
{
    public function __construct(
        private CerealPorDiaRepositoryInterface $cerealPorDiaRepository
    ) {}

    public function find(int $id): ?CerealPorDia
    {
        return $this->cerealPorDiaRepository->find($id);
    }

    /** @return Collection<int, CerealPorDia> */
    public function todos(): Collection
    {
        return $this->cerealPorDiaRepository->todos();
    }

    public function crear(array $data): CerealPorDia
    {
        $cereal = new CerealPorDia($data);
        return $this->cerealPorDiaRepository->guardar($cereal);
    }

    public function actualizar(int $id, array $data): CerealPorDia
    {
        $cereal = $this->cerealPorDiaRepository->find($id);
        if (!$cereal) {
            throw new \InvalidArgumentException('Cereal por día no encontrado.');
        }
        $cereal->fill($data);
        return $this->cerealPorDiaRepository->guardar($cereal);
    }

    public function eliminar(int $id): bool
    {
        $cereal = $this->cerealPorDiaRepository->find($id);
        if (!$cereal) {
            return false;
        }
        return $this->cerealPorDiaRepository->eliminar($cereal);
    }
}
