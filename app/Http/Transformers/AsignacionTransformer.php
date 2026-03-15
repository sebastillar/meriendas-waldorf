<?php

namespace App\Http\Transformers;

use App\Domain\Models\Asignacion;
use League\Fractal\TransformerAbstract;

class AsignacionTransformer extends TransformerAbstract
{
    /**
     * @return array<string, mixed>
     */
    public function transform(Asignacion $asignacion): array
    {
        return [
            'id' => $asignacion->id,
            'fecha' => $asignacion->fecha->toDateString(),
            'cereal' => $asignacion->cereal,
            'estado' => $asignacion->estado,
            'fruta' => [
                'id' => $asignacion->alumno_fruta_id,
                'nombre' => $asignacion->alumnoFruta?->nombre ?? '',
            ],
            'elaboracion' => [
                'id' => $asignacion->alumno_elaboracion_id,
                'nombre' => $asignacion->alumnoElaboracion?->nombre ?? '',
            ],
        ];
    }
}
