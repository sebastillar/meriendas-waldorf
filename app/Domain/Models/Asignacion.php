<?php

namespace App\Domain\Models;

use Database\Factories\AsignacionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asignacion extends Model
{
    use HasFactory;
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

    protected static function newFactory(): AsignacionFactory
    {
        return AsignacionFactory::new();
    }

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

