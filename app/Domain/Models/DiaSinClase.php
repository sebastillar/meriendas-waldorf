<?php

namespace App\Domain\Models;

use Database\Factories\DiaSinClaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaSinClase extends Model
{
    use HasFactory;

    protected $table = 'dias_sin_clase';

    protected $fillable = [
        'fecha',
        'motivo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    protected static function newFactory(): DiaSinClaseFactory
    {
        return DiaSinClaseFactory::new();
    }
}
