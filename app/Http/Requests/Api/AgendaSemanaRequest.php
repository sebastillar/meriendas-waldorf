<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AgendaSemanaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'fecha_inicio' => ['nullable', 'date'],
        ];
    }

    /**
     * Query parameters para la documentación (Scribe).
     *
     * @return array<string, array{description: string, example: string|null}>
     */
    public function queryParameters(): array
    {
        return [
            'fecha_inicio' => [
                'description' => 'Fecha de inicio de la semana (YYYY-MM-DD). Opcional; si no se envía, se usa la semana actual.',
                'example' => null,
            ],
        ];
    }
}
