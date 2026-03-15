<?php

namespace App\Domain\Services;

/**
 * Servicio para la funcionalidad "destacar niño" en la agenda pública.
 * Obtiene alumnos para el selector y estadísticas de la familia destacada;
 * no accede a modelos ni repositorios directamente, solo a otros servicios.
 */
class AgendaDestacadoService
{
    public function __construct(
        private AlumnoService $alumnoService,
        private EstadisticasService $estadisticasService
    ) {}

    /**
     * Devuelve los datos necesarios para la vista de agenda con destacado (selector y estadísticas).
     * Solo si hay consentimiento de cookie se incluyen alumnos para el selector.
     * Solo si hay alumno destacado se incluyen estadísticas de su familia.
     *
     * @return array{
     *   alumnos_para_selector: array<int, array{id: int, nombre: string, apellido: string}>,
     *   estadisticas_familia: array{veces_fruta: int, veces_elaboracion: int, fecha_cumpleanos: ?string, recolectando_para_regalo: bool, apellido: string}|null
     * }
     */
    public function getDatosDestacado(bool $cookieConsent, ?int $destacadoAlumnoId): array
    {
        $alumnosParaSelector = [];
        if ($cookieConsent) {
            $alumnosParaSelector = $this->alumnoService->activos()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'nombre' => $a->nombre,
                    'apellido' => $a->familia?->nombre_para_listado ?? '',
                ])
                ->values()
                ->all();
        }

        $estadisticasFamilia = null;
        if ($destacadoAlumnoId) {
            $alumno = $this->alumnoService->find($destacadoAlumnoId);
            if ($alumno && $alumno->familia_id) {
                $estadisticasFamilia = $this->estadisticasService->estadisticasFamilia($alumno->familia_id);
            }
        }

        return [
            'alumnos_para_selector' => $alumnosParaSelector,
            'estadisticas_familia' => $estadisticasFamilia,
        ];
    }
}
