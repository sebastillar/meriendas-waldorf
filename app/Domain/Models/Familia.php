<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Familia extends Model
{
    protected $table = 'familias';

    protected $fillable = [
        'nombre_madre',
        'email_madre',
        'nombre_padre',
        'email_padre',
        'familia_regalo_id',
        'banco',
        'numero_cuenta',
        'tipo_cuenta',
        'nombre_cuenta',
        'moneda',
        'activo',
    ];

    protected $casts = [
        'activo' => 'bool',
    ];

    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class);
    }

    public function familiaParaRegalo(): BelongsTo
    {
        return $this->belongsTo(Familia::class, 'familia_regalo_id');
    }

    public function familiasQueMeRegalan(): HasMany
    {
        return $this->hasMany(Familia::class, 'familia_regalo_id');
    }

    /**
     * Nombre para listados/selects: primer hijo o "Familia #id".
     */
    public function getNombreParaListadoAttribute(): string
    {
        $primero = $this->alumnos->sortBy('id')->first();
        return $primero?->nombre ?? 'Familia #' . $this->id;
    }
}

