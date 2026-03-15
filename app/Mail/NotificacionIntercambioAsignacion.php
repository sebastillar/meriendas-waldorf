<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionIntercambioAsignacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $fecha,
        public string $rol,
        public string $nombreAlumno
    ) {}

    public function envelope(): Envelope
    {
        $tarea = $this->rol === 'fruta' ? 'fruta' : 'elaboración';
        return new Envelope(
            subject: 'Te asignaron ' . $tarea . ' para el ' . $this->fecha,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacion-intercambio-asignacion',
        );
    }
}
