<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecordatorioMerienda extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $rol,
        public string $nombreAlumno,
        public string $fecha
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio: mañana te toca ' . ($this->rol === 'fruta' ? 'fruta' : 'elaboración'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recordatorio-merienda',
        );
    }
}
