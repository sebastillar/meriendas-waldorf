<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Intercambio extends Model
{
    protected $table = 'intercambios';

    protected $fillable = [
        'asignacion_id',
        'rol',
        'alumno_original_id',
        'alumno_nuevo_id',
        'motivo',
    ];

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class);
    }

    public function alumnoOriginal(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_original_id');
    }

    public function alumnoNuevo(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_nuevo_id');
    }
}

