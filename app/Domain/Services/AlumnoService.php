<?php

namespace App\Domain\Services;

use App\Domain\Models\Alumno;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AlumnoService
{
    public function __construct(
        private AlumnoRepositoryInterface $alumnoRepository,
        private RecalculoAsignacionesService $recalculoAsignacionesService
    ) {}

    public function find(int $id): ?Alumno
    {
        return $this->alumnoRepository->find($id);
    }

    /** @return Collection<int, Alumno> */
    public function todos(): Collection
    {
        return $this->alumnoRepository->todos();
    }

    /** @return Collection<int, Alumno> */
    public function activos(): Collection
    {
        return $this->alumnoRepository->activos();
    }

    /**
     * Lista de alumnos activos para el dropdown de filtros (id, nombre, apellido/familia para mostrar).
     * @return array<int, array{id: int, nombre: string, apellido: string}>
     */
    public function activosParaFiltro(): array
    {
        return $this->alumnoRepository->activos()
            ->map(fn (Alumno $a) => [
                'id' => $a->id,
                'nombre' => $a->nombre,
                'apellido' => $a->familia?->nombre_para_listado ?? '',
            ])
            ->values()
            ->all();
    }

    public function crear(array $data): Alumno
    {
        $alumno = new Alumno($data);
        return $this->alumnoRepository->guardar($alumno);
    }

    public function actualizar(int $id, array $data): Alumno
    {
        $alumno = $this->alumnoRepository->find($id);
        if (!$alumno) {
            throw new \InvalidArgumentException('Alumno no encontrado.');
        }
        $alumno->fill($data);
        return $this->alumnoRepository->guardar($alumno);
    }

    public function eliminar(int $id): bool
    {
        $alumno = $this->alumnoRepository->find($id);
        if (!$alumno) {
            return false;
        }
        return $this->alumnoRepository->eliminar($alumno);
    }

    public function darDeBaja(int $id): Alumno
    {
        $alumno = $this->alumnoRepository->find($id);
        if (!$alumno) {
            throw new \InvalidArgumentException('Alumno no encontrado.');
        }
        if (!$alumno->activo) {
            return $alumno;
        }
        $alumno->activo = false;
        $this->alumnoRepository->guardar($alumno);
        $this->recalculoAsignacionesService->recalcularFuturasDesde(Carbon::today());
        return $this->alumnoRepository->find($id);
    }

    public function darDeAlta(int $id): Alumno
    {
        $alumno = $this->alumnoRepository->find($id);
        if (!$alumno) {
            throw new \InvalidArgumentException('Alumno no encontrado.');
        }
        if ($alumno->activo) {
            return $alumno;
        }
        $alumno->activo = true;
        $this->alumnoRepository->guardar($alumno);
        $this->recalculoAsignacionesService->recalcularFuturasDesde(Carbon::today());
        return $this->alumnoRepository->find($id);
    }
}
