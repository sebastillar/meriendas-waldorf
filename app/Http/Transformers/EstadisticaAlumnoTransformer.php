<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class EstadisticaAlumnoTransformer extends TransformerAbstract
{
    /**
     * @param array{alumno_id: int, nombre: string, fruta: int, elaboracion: int, por_mes: array} $row
     * @return array<string, mixed>
     */
    public function transform(array $row): array
    {
        return [
            'alumno_id' => $row['alumno_id'],
            'nombre' => $row['nombre'],
            'fruta' => $row['fruta'],
            'elaboracion' => $row['elaboracion'],
            'por_mes' => $row['por_mes'],
        ];
    }
}
