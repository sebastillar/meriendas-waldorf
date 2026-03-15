<?php

namespace App\Domain\Contracts;

/**
 * Contrato para enviar recordatorios de merienda (email, WhatsApp en el futuro, etc.).
 */
interface RecordatorioNotifierInterface
{
    /**
     * @param array{rol: string, nombre_alumno: string, fecha: string} $datos
     */
    public function enviarRecordatorio(string $email, array $datos): void;
}
