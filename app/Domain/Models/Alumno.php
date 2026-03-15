<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alumno extends Model
{
    protected $table = 'alumnos';

    protected $fillable = [
        'familia_id',
        'nombre',
        'fecha_cumpleanos',
        'activo',
    ];

    protected $casts = [
        'activo' => 'bool',
        'fecha_cumpleanos' => 'date',
    ];

    public function familia(): BelongsTo
    {
        return $this->belongsTo(Familia::class);
    }

    public function asignacionesFruta(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'alumno_fruta_id');
    }

    public function asignacionesElaboracion(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'alumno_elaboracion_id');
    }
}

