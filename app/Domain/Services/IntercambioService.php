<?php

namespace App\Domain\Services;

use App\Domain\Contracts\NotificadorIntercambioInterface;
use App\Domain\Models\Asignacion;
use App\Domain\Models\Intercambio;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\AsignacionRepositoryInterface;

class IntercambioService
{
    public function __construct(
        private AsignacionRepositoryInterface $asignacionRepository,
        private AlumnoRepositoryInterface $alumnoRepository,
        private NotificadorIntercambioInterface $notificadorIntercambio
    ) {}

    public function intercambiar(int $asignacionId, string $rol, int $alumnoNuevoId, ?string $motivo = null): Asignacion
    {
        $asignacion = $this->asignacionRepository->find($asignacionId);
        if (!$asignacion) {
            throw new \InvalidArgumentException('Asignación no encontrada.');
        }

        $alumnoNuevo = $this->alumnoRepository->find($alumnoNuevoId);
        if (!$alumnoNuevo || !$alumnoNuevo->activo) {
            throw new \InvalidArgumentException('Alumno no válido o inactivo.');
        }

        $rol = strtolower($rol);
        if (!in_array($rol, ['fruta', 'elaboracion'], true)) {
            throw new \InvalidArgumentException('Rol debe ser fruta o elaboracion.');
        }

        if ($rol === 'fruta') {
            $alumnoOriginalId = $asignacion->alumno_fruta_id;
            if ($alumnoOriginalId === $alumnoNuevoId) {
                return $asignacion;
            }
            $asignacion->alumno_fruta_id = $alumnoNuevoId;
        } else {
            $alumnoOriginalId = $asignacion->alumno_elaboracion_id;
            if ($alumnoOriginalId === $alumnoNuevoId) {
                return $asignacion;
            }
            $asignacion->alumno_elaboracion_id = $alumnoNuevoId;
        }

        \DB::transaction(function () use ($asignacion, $rol, $alumnoOriginalId, $alumnoNuevoId, $motivo) {
            $this->asignacionRepository->guardar($asignacion);
            Intercambio::create([
                'asignacion_id' => $asignacion->id,
                'rol' => $rol,
                'alumno_original_id' => $alumnoOriginalId,
                'alumno_nuevo_id' => $alumnoNuevoId,
                'motivo' => $motivo,
            ]);
        });

        $asignacion->estado = 'intercambiada';
        $this->asignacionRepository->guardar($asignacion);

        $this->notificarFamiliaAsignada($asignacion, $alumnoNuevo, $rol);

        return $this->asignacionRepository->find($asignacion->id);
    }

    private function notificarFamiliaAsignada(Asignacion $asignacion, $alumnoNuevo, string $rol): void
    {
        $familia = $alumnoNuevo->familia;
        if (!$familia) {
            return;
        }
        $fechaFormato = $asignacion->fecha->locale('es')->translatedFormat('l d/m/Y');
        $datos = [
            'fecha' => $fechaFormato,
            'rol' => $rol,
            'nombre_alumno' => $alumnoNuevo->nombre,
        ];
        $emails = array_filter([$familia->email_madre ?? '', $familia->email_padre ?? '']);
        foreach (array_unique($emails) as $email) {
            if ($email !== '') {
                $this->notificadorIntercambio->enviarNotificacionIntercambio($email, $datos);
            }
        }
    }
}
