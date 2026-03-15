<?php

namespace App\Notifications;

use App\Domain\Contracts\NotificadorIntercambioInterface;
use App\Mail\NotificacionIntercambioAsignacion;
use Illuminate\Support\Facades\Mail;

class NotificadorIntercambioMail implements NotificadorIntercambioInterface
{
    public function enviarNotificacionIntercambio(string $email, array $datos): void
    {
        if ($email === '') {
            return;
        }
        Mail::to($email)->send(new NotificacionIntercambioAsignacion(
            $datos['fecha'],
            $datos['rol'],
            $datos['nombre_alumno']
        ));
    }
}
