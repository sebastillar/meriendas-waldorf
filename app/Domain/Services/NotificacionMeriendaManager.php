<?php

namespace App\Domain\Services;

use App\Domain\Models\NotificacionMerienda;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use App\Mail\RecordatorioMerienda;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class NotificacionMeriendaManager
{
    public function __construct(
        private AsignacionRepositoryInterface $asignacionRepository,
    ) {}

    public function prepararParaFecha(Carbon $fechaObjetivo): void
    {
        $asig = $this->asignacionRepository->getPorFecha($fechaObjetivo);
        if (! $asig) {
            return;
        }

        $fechaString = $fechaObjetivo->toDateString();
        $fechaFormato = $fechaObjetivo->locale('es')->translatedFormat('l d/m/Y');

        $this->crearPendientesParaAlumnoFamilia(
            $fechaString,
            'fruta',
            $asig->alumnoFruta?->familia,
            $asig->alumnoFruta?->nombre,
            $fechaFormato
        );

        $this->crearPendientesParaAlumnoFamilia(
            $fechaString,
            'elaboracion',
            $asig->alumnoElaboracion?->familia,
            $asig->alumnoElaboracion?->nombre,
            $fechaFormato
        );
    }

    private function crearPendientesParaAlumnoFamilia(
        string $fechaString,
        string $rol,
        $familia,
        ?string $nombreAlumno,
        string $fechaFormato
    ): void {
        if (! $familia || ! $nombreAlumno) {
            return;
        }
        $emails = array_filter([
            $familia->email_madre ?? '',
            $familia->email_padre ?? '',
        ]);

        foreach (array_unique($emails) as $email) {
            if ($email === '') {
                continue;
            }
            NotificacionMerienda::firstOrCreate(
                [
                    'fecha_envio_programada' => $fechaString,
                    'tipo' => 'recordatorio_merienda',
                    'email' => $email,
                ],
                [
                    'rol' => $rol,
                    'nombre_alumno' => $nombreAlumno,
                    'estado' => 'pendiente',
                    'intentos' => 0,
                ]
            );
        }
    }

    public function enviarPendientesParaFecha(Carbon $fechaObjetivo, int $maxIntentos = 2): void
    {
        $fechaString = $fechaObjetivo->toDateString();

        $pendientes = NotificacionMerienda::where('fecha_envio_programada', $fechaString)
            ->whereIn('estado', ['pendiente', 'fallido'])
            ->where('intentos', '<', $maxIntentos)
            ->get();

        foreach ($pendientes as $notif) {
            $this->enviar($notif);
        }
    }

    public function reenviar(NotificacionMerienda $notif): void
    {
        $this->enviar($notif, true);
    }

    private function enviar(NotificacionMerienda $notif, bool $forzar = false): void
    {
        if (! $forzar && $notif->estado === 'enviado') {
            return;
        }

        $datosMail = [
            'rol' => $notif->rol,
            'nombre_alumno' => $notif->nombre_alumno,
            'fecha' => $notif->fecha_envio_programada?->locale('es')->translatedFormat('l d/m/Y') ?? $notif->fecha_envio_programada,
        ];

        try {
            Mail::to($notif->email)->send(
                new RecordatorioMerienda($datosMail['rol'], $datosMail['nombre_alumno'], $datosMail['fecha'])
            );
            $notif->estado = 'enviado';
            $notif->error_ultimo_intento = null;
        } catch (\Throwable $e) {
            $notif->estado = 'fallido';
            $notif->error_ultimo_intento = $e->getMessage();
        }

        $notif->intentos = (int) $notif->intentos + 1;
        $notif->ultimo_intento_at = now();
        $notif->save();
    }
}

