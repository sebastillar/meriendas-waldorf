<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Colores del día (ritmo semanal) en pedagogía Waldorf y estilos de agenda.
 */
final class ColoresDiaWaldorf
{
    /**
     * Tarjetas para la página "Colores por día" (lunes a viernes lectivo).
     *
     * @return list<array{
     *     slug: string,
     *     nombre_dia: string,
     *     nombre_color: string,
     *     descripcion: string,
     *     blob_primary: string,
     *     blob_secondary: string,
     *     accent_text: string,
     *     border_soft: string
     * }>
     */
    public static function diasSemana(): array
    {
        return [
            [
                'slug' => 'lun',
                'nombre_dia' => 'Lunes',
                'nombre_color' => 'Azul',
                'descripcion' => 'Día tranquilo, para empezar despacio',
                'blob_primary' => 'bg-sky-400/50',
                'blob_secondary' => 'bg-indigo-300/40',
                'accent_text' => 'text-sky-900',
                'border_soft' => 'border-sky-200/80',
            ],
            [
                'slug' => 'mar',
                'nombre_dia' => 'Martes',
                'nombre_color' => 'Rojo',
                'descripcion' => 'Día con más energía y movimiento',
                'blob_primary' => 'bg-rose-400/45',
                'blob_secondary' => 'bg-red-300/35',
                'accent_text' => 'text-rose-950',
                'border_soft' => 'border-rose-200/80',
            ],
            [
                'slug' => 'mie',
                'nombre_dia' => 'Miércoles',
                'nombre_color' => 'Amarillo',
                'descripcion' => 'Día alegre y luminoso',
                'blob_primary' => 'bg-amber-300/50',
                'blob_secondary' => 'bg-yellow-200/45',
                'accent_text' => 'text-amber-950',
                'border_soft' => 'border-amber-200/80',
            ],
            [
                'slug' => 'jue',
                'nombre_dia' => 'Jueves',
                'nombre_color' => 'Naranja',
                'descripcion' => 'Día creativo',
                'blob_primary' => 'bg-orange-400/45',
                'blob_secondary' => 'bg-amber-400/35',
                'accent_text' => 'text-orange-950',
                'border_soft' => 'border-orange-200/80',
            ],
            [
                'slug' => 'vie',
                'nombre_dia' => 'Viernes',
                'nombre_color' => 'Verde',
                'descripcion' => 'Día de naturaleza y calma',
                'blob_primary' => 'bg-emerald-400/40',
                'blob_secondary' => 'bg-lime-300/35',
                'accent_text' => 'text-emerald-950',
                'border_soft' => 'border-emerald-200/80',
            ],
        ];
    }

    /**
     * Clase CSS para filas de la agenda con merienda (lun–vie lectivo).
     * Vacío si es feriado / sin clase / fin de semana.
     */
    public static function claseFilaAgenda(string $fechaYmd, bool $esFeriado): string
    {
        if ($esFeriado) {
            return '';
        }

        $n = (int) Carbon::parse($fechaYmd)->format('N');

        return match ($n) {
            1 => 'agenda-col-lun',
            2 => 'agenda-col-mar',
            3 => 'agenda-col-mie',
            4 => 'agenda-col-jue',
            5 => 'agenda-col-vie',
            default => '',
        };
    }
}
