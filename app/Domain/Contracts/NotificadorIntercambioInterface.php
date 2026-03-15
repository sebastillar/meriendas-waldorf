<?php

namespace App\Domain\Contracts;

/**
 * Notifica a la familia que quedó asignada tras un intercambio (debe llevar fruta o elaboración).
 */
interface NotificadorIntercambioInterface
{
    /**
     * @param array{fecha: string, rol: string, nombre_alumno: string} $datos
     */
    public function enviarNotificacionIntercambio(string $email, array $datos): void;
}
