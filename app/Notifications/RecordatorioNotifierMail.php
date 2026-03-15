<?php

namespace App\Notifications;

use App\Domain\Contracts\RecordatorioNotifierInterface;
use App\Mail\RecordatorioMerienda;
use Illuminate\Support\Facades\Mail;

class RecordatorioNotifierMail implements RecordatorioNotifierInterface
{
    public function enviarRecordatorio(string $email, array $datos): void
    {
        if ($email === '') {
            return;
        }
        Mail::to($email)->send(new RecordatorioMerienda(
            $datos['rol'],
            $datos['nombre_alumno'],
            $datos['fecha']
        ));
    }
}
