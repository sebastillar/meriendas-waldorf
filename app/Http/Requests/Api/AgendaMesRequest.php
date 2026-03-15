<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AgendaMesRequest extends FormRequest
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
            'anio' => ['required', 'integer', 'min:2020', 'max:2100'],
            'mes' => ['required', 'integer', 'min:1', 'max:12'],
        ];
    }

    /**
     * Query parameters para la documentación (Scribe).
     *
     * @return array<string, array{description: string, example: int}>
     */
    public function queryParameters(): array
    {
        return [
            'anio' => [
                'description' => 'Año (entre 2020 y 2100).',
                'example' => (int) date('Y'),
            ],
            'mes' => [
                'description' => 'Mes (1 a 12).',
                'example' => (int) date('n'),
            ],
        ];
    }
}
