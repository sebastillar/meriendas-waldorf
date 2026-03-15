<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asignacion extends Model
{
    protected $table = 'asignaciones';

    protected $fillable = [
        'fecha',
        'alumno_fruta_id',
        'alumno_elaboracion_id',
        'cereal',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function alumnoFruta(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_fruta_id');
    }

    public function alumnoElaboracion(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_elaboracion_id');
    }

    public function intercambios(): HasMany
    {
        return $this->hasMany(Intercambio::class);
    }
}

