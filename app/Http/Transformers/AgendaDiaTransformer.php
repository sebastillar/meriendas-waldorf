<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class AgendaDiaTransformer extends TransformerAbstract
{
    /**
     * @param array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool, etiqueta_feriado: string} $row
     * @return array<string, mixed>
     */
    public function transform(array $row): array
    {
        return [
            'fecha' => $row['fecha'],
            'dia' => $row['dia'],
            'cereal' => $row['cereal'],
            'fruta' => $row['fruta'],
            'elaboracion' => $row['elaboracion'],
            'es_feriado' => $row['es_feriado'],
            'etiqueta_feriado' => $row['etiqueta_feriado'] ?? '',
        ];
    }
}
