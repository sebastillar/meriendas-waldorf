<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionCalendario extends Model
{
    protected $table = 'configuracion_calendario';

    protected $fillable = [
        'anio',
        'fecha_inicio_clases',
    ];

    protected $casts = [
        'anio' => 'integer',
        'fecha_inicio_clases' => 'date',
    ];
}

