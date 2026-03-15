<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class DiaSinClase extends Model
{
    protected $table = 'dias_sin_clase';

    protected $fillable = [
        'fecha',
        'motivo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];
}

