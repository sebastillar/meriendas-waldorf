<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class IntercambiarAsignacionRequest extends FormRequest
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
            'rol' => ['required', 'string', 'in:fruta,elaboracion'],
            'alumno_nuevo_id' => ['required', 'integer', 'exists:alumnos,id'],
            'motivo' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Body parameters para la documentación (Scribe).
     *
     * @return array<string, array{description: string, example: string|int|null}>
     */
    public function bodyParameters(): array
    {
        return [
            'rol' => [
                'description' => 'Rol a intercambiar: `fruta` o `elaboracion`.',
                'example' => 'fruta',
            ],
            'alumno_nuevo_id' => [
                'description' => 'ID del alumno que tomará el rol.',
                'example' => 1,
            ],
            'motivo' => [
                'description' => 'Motivo del intercambio (opcional, máx. 500 caracteres).',
                'example' => null,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rol.required' => 'El rol (fruta o elaboración) es obligatorio.',
            'rol.in' => 'El rol debe ser fruta o elaboracion.',
            'alumno_nuevo_id.required' => 'Debe indicar el alumno con quien intercambiar.',
            'alumno_nuevo_id.exists' => 'El alumno seleccionado no existe.',
        ];
    }
}
