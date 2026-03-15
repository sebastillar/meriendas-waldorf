<?php

namespace App\Domain\Services;

use App\Domain\Models\Alumno;
use App\Domain\Models\Familia;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\FamiliaRepositoryInterface;
use Carbon\Carbon;

/**
 * Lógica de "quién está recolectando" para el regalo de cumpleaños: por próximo cumpleaños (alumno).
 */
class RecolectandoService
{
    public function __construct(
        private AlumnoRepositoryInterface $alumnoRepository,
        private FamiliaRepositoryInterface $familiaRepository,
        private RecolectaAportesService $recolectaAportesService
    ) {}

    /**
     * Familia que está recolectando y alumno para quien se recolecta (próximo cumpleaños).
     *
     * @return array{familia_recolectora: Familia, alumno_beneficiario: Alumno}|null
     */
    public function recolectaActual(): ?array
    {
        $alumnoProximo = $this->alumnoConProximoCumpleanos();
        if (!$alumnoProximo || !$alumnoProximo->familia_id) {
            return null;
        }

        $familiaBeneficiaria = $alumnoProximo->familia;
        $recolectora = $this->familiaRepository->activas()->first(
            fn (Familia $f) => (int) $f->familia_regalo_id === (int) $familiaBeneficiaria->id
        );

        if (!$recolectora) {
            return null;
        }

        $familiaBeneficiariaId = (int) $familiaBeneficiaria->id;
        $aportaron = $this->recolectaAportesService->getAlumnoIdsQueAportaron($familiaBeneficiariaId);
        $totalPosibles = $this->recolectaAportesService->alumnosParaAportes($familiaBeneficiariaId);

        return [
            'familia_recolectora' => $recolectora,
            'alumno_beneficiario' => $alumnoProximo,
            'aportaron_count' => count($aportaron),
            'total_count' => count($totalPosibles),
        ];
    }

    private function alumnoConProximoCumpleanos(): ?Alumno
    {
        $hoy = Carbon::today();
        $alumnos = $this->alumnoRepository->activos()->filter(
            fn (Alumno $a) => $a->fecha_cumpleanos !== null
        );

        $proximo = null;
        $proximaFecha = null;

        foreach ($alumnos as $alumno) {
            $cumple = $alumno->fecha_cumpleanos;
            $esteAnio = Carbon::createFromDate($hoy->year, $cumple->month, $cumple->day);
            $siguiente = $esteAnio->lt($hoy)
                ? $esteAnio->copy()->addYear()
                : $esteAnio;

            if ($proximaFecha === null || $siguiente->lt($proximaFecha)) {
                $proximaFecha = $siguiente;
                $proximo = $alumno;
            }
        }

        return $proximo;
    }

    /**
     * Información del próximo cumpleaños (alumno activo con fecha), aunque no haya familia recolectora asignada.
     * Útil para mostrar en el widget cuando no hay recolecta activa.
     *
     * @return array{nombre: string, fecha_formato: string}|null
     */
    public function proximoCumpleanosSinRecolectora(): ?array
    {
        $alumno = $this->alumnoConProximoCumpleanos();
        if (!$alumno) {
            return null;
        }
        $hoy = Carbon::today();
        $cumple = $alumno->fecha_cumpleanos;
        $esteAnio = Carbon::createFromDate($hoy->year, $cumple->month, $cumple->day);
        $siguiente = $esteAnio->lt($hoy) ? $esteAnio->copy()->addYear() : $esteAnio;
        return [
            'nombre' => $alumno->nombre,
            'fecha_formato' => $siguiente->format('d/m/Y'),
        ];
    }
}
