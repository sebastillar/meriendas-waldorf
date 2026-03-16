<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionMerienda extends Model
{
    protected $table = 'notificaciones_merienda';

    protected $fillable = [
        'fecha_envio_programada',
        'tipo',
        'email',
        'rol',
        'nombre_alumno',
        'estado',
        'intentos',
        'ultimo_intento_at',
        'error_ultimo_intento',
    ];

    protected $casts = [
        'fecha_envio_programada' => 'date',
        'intentos' => 'int',
        'ultimo_intento_at' => 'datetime',
    ];
}

