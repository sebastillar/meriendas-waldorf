<?php

namespace App\Domain\Models;

use Database\Factories\ConfiguracionCalendarioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionCalendario extends Model
{
    use HasFactory;

    protected $table = 'configuracion_calendario';

    protected $fillable = [
        'anio',
        'fecha_inicio_clases',
        'fecha_fin_clases',
    ];

    protected $casts = [
        'anio' => 'integer',
        'fecha_inicio_clases' => 'date',
        'fecha_fin_clases' => 'date',
    ];

    protected static function newFactory(): ConfiguracionCalendarioFactory
    {
        return ConfiguracionCalendarioFactory::new();
    }
}
