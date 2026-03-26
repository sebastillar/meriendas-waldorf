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

    /**
     * Colectas de cumpleaños del mes actual (alumnos activos con cumpleaños en el mes).
     *
     * @return array<int, array{
     *   alumno_beneficiario: Alumno,
     *   familia_beneficiaria: Familia,
     *   familia_recolectora: ?Familia,
     *   fecha_cumpleanos: \Carbon\Carbon,
     *   estado: string,
     *   aportaron_count: int,
     *   total_count: int
     * }>
     */
    public function recolectasDelMesActual(): array
    {
        $hoy = Carbon::today();
        $mes = (int) $hoy->month;
        $anio = (int) $hoy->year;

        $alumnos = $this->alumnoRepository->activos()
            ->filter(fn (Alumno $a) => $a->fecha_cumpleanos !== null && (int) $a->fecha_cumpleanos->month === $mes)
            ->values();

        if ($alumnos->isEmpty()) {
            return [];
        }

        $familiasActivas = $this->familiaRepository->activas();

        $out = [];
        foreach ($alumnos as $alumno) {
            if (!$alumno->familia_id || !$alumno->familia) {
                continue;
            }

            $familiaBeneficiaria = $alumno->familia;
            $familiaBeneficiariaId = (int) $familiaBeneficiaria->id;

            $cumple = $alumno->fecha_cumpleanos;
            $fechaCumpleEsteAnio = Carbon::createFromDate($anio, $cumple->month, $cumple->day)->startOfDay();

            $recolectora = $familiasActivas->first(
                fn (Familia $f) => (int) $f->familia_regalo_id === $familiaBeneficiariaId
            );

            $aportaron = $this->recolectaAportesService->getAlumnoIdsQueAportaron($familiaBeneficiariaId);
            $totalPosibles = $this->recolectaAportesService->alumnosParaAportes($familiaBeneficiariaId);

            $estado = $recolectora ? 'activa' : 'sin_recolectora';

            $out[] = [
                'alumno_beneficiario' => $alumno,
                'familia_beneficiaria' => $familiaBeneficiaria,
                'familia_recolectora' => $recolectora ?: null,
                'fecha_cumpleanos' => $fechaCumpleEsteAnio,
                'estado' => $estado,
                'aportaron_count' => count($aportaron),
                'total_count' => count($totalPosibles),
            ];
        }

        usort($out, fn ($a, $b) => $a['fecha_cumpleanos']->lt($b['fecha_cumpleanos']) ? -1 : 1);

        return $out;
    }

    /**
     * Cumpleaños de todo el año (alumnos activos con fecha cargada) y su familia encargada de la colecta.
     *
     * @return array<int, array{
     *   alumno: Alumno,
     *   fecha_cumpleanos: \Carbon\Carbon,
     *   familia_encargada: ?Familia
     * }>
     */
    public function cumpleanosConFamiliaEncargada(): array
    {
        $anio = (int) Carbon::today()->year;
        $familiasActivas = $this->familiaRepository->activas();

        $alumnos = $this->alumnoRepository->activos()
            ->filter(fn (Alumno $a) => $a->fecha_cumpleanos !== null)
            ->values();

        $out = [];
        foreach ($alumnos as $alumno) {
            if (!$alumno->familia_id || !$alumno->familia) {
                continue;
            }

            $familiaBeneficiariaId = (int) $alumno->familia->id;
            $familiaEncargada = $familiasActivas->first(
                fn (Familia $f) => (int) $f->familia_regalo_id === $familiaBeneficiariaId
            );
            $cumple = $alumno->fecha_cumpleanos;
            $fechaCumpleEsteAnio = Carbon::createFromDate($anio, $cumple->month, $cumple->day)->startOfDay();

            $out[] = [
                'alumno' => $alumno,
                'fecha_cumpleanos' => $fechaCumpleEsteAnio,
                'familia_encargada' => $familiaEncargada ?: null,
            ];
        }

        usort($out, fn ($a, $b) => $a['fecha_cumpleanos']->lt($b['fecha_cumpleanos']) ? -1 : 1);

        return $out;
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
