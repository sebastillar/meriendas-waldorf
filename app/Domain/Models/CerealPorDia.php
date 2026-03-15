<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class CerealPorDia extends Model
{
    protected $table = 'cereales_por_dia';

    protected $fillable = [
        'dia_semana',
        'cereal',
        'activo',
    ];

    protected $casts = [
        'dia_semana' => 'int',
        'activo' => 'bool',
    ];
}

