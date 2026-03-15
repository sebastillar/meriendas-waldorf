<?php

namespace App\Domain\Services;

use App\Domain\Contracts\RecordatorioNotifierInterface;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use Carbon\Carbon;

/**
 * Prepara y envía recordatorios a las familias que tienen turno al día siguiente
 * (fruta o elaboración). Si hoy es viernes, incluye también el lunes.
 * Usa solo AsignacionRepository y el notificador inyectado.
 */
class RecordatorioMeriendaService
{
    public function __construct(
        private AsignacionRepositoryInterface $asignacionRepository,
        private RecordatorioNotifierInterface $notifier
    ) {}

    public function enviarRecordatorios(): void
    {
        $hoy = Carbon::today();
        $canales = config('recordatorio.canales', ['mail']);
        if (! in_array('mail', $canales, true)) {
            return;
        }

        $this->enviarParaFecha($hoy->copy()->addDay());
        if ($hoy->isFriday()) {
            $this->enviarParaFecha($hoy->copy()->addDays(3));
        }
    }

    private function enviarParaFecha(Carbon $fecha): void
    {
        $asig = $this->asignacionRepository->getPorFecha($fecha);
        if (! $asig) {
            return;
        }

        $fechaFormato = $fecha->locale('es')->translatedFormat('l d/m/Y');

        if ($asig->alumnoFruta && $asig->alumnoFruta->familia) {
            $familia = $asig->alumnoFruta->familia;
            $datos = [
                'rol' => 'fruta',
                'nombre_alumno' => $asig->alumnoFruta->nombre,
                'fecha' => $fechaFormato,
            ];
            $this->enviarAFamilia($familia, $datos);
        }

        if ($asig->alumnoElaboracion && $asig->alumnoElaboracion->familia) {
            $familia = $asig->alumnoElaboracion->familia;
            $datos = [
                'rol' => 'elaboracion',
                'nombre_alumno' => $asig->alumnoElaboracion->nombre,
                'fecha' => $fechaFormato,
            ];
            $this->enviarAFamilia($familia, $datos);
        }
    }

    private function enviarAFamilia($familia, array $datos): void
    {
        $emails = array_filter([
            $familia->email_madre ?? '',
            $familia->email_padre ?? '',
        ]);
        foreach (array_unique($emails) as $email) {
            if ($email !== '') {
                $this->notifier->enviarRecordatorio($email, $datos);
            }
        }
    }
}
