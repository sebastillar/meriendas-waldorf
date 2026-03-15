<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecolectaAporte extends Model
{
    protected $table = 'recolecta_aportes';

    protected $fillable = [
        'familia_beneficiaria_id',
        'alumno_id',
    ];

    public function familiaBeneficiaria(): BelongsTo
    {
        return $this->belongsTo(Familia::class, 'familia_beneficiaria_id');
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }
}
