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
     *     cereal: string,
     *     planeta: string,
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
                'cereal' => 'Arroz',
                'planeta' => 'Luna',
                'simbolo_planeta' => '☽',
                'descripcion' => 'Día tranquilo, para empezar despacio',
                'blob_primary' => 'bg-sky-400/50',
                'blob_secondary' => 'bg-indigo-300/40',
                'accent_text' => 'text-sky-900',
                'border_soft' => 'border-sky-200/80',
                'bg_tint_css' => 'rgba(56,189,248,0.09)',
                'border_color_css' => 'rgba(56,189,248,0.55)',
                'tag_bg_css' => 'rgba(56,189,248,0.16)',
                'tag_text_css' => '#0369a1',
            ],
            [
                'slug' => 'mar',
                'nombre_dia' => 'Martes',
                'nombre_color' => 'Rojo',
                'cereal' => 'Cebada',
                'planeta' => 'Marte',
                'simbolo_planeta' => '♂',
                'descripcion' => 'Día con más energía y movimiento',
                'blob_primary' => 'bg-rose-400/45',
                'blob_secondary' => 'bg-red-300/35',
                'accent_text' => 'text-rose-950',
                'border_soft' => 'border-rose-200/80',
                'bg_tint_css' => 'rgba(251,113,133,0.09)',
                'border_color_css' => 'rgba(251,113,133,0.55)',
                'tag_bg_css' => 'rgba(251,113,133,0.16)',
                'tag_text_css' => '#9f1239',
            ],
            [
                'slug' => 'mie',
                'nombre_dia' => 'Miércoles',
                'nombre_color' => 'Amarillo',
                'cereal' => 'Mijo',
                'planeta' => 'Mercurio',
                'simbolo_planeta' => '☿',
                'descripcion' => 'Día alegre y luminoso',
                'blob_primary' => 'bg-amber-300/50',
                'blob_secondary' => 'bg-yellow-200/45',
                'accent_text' => 'text-amber-950',
                'border_soft' => 'border-amber-200/80',
                'bg_tint_css' => 'rgba(252,211,77,0.10)',
                'border_color_css' => 'rgba(217,119,6,0.45)',
                'tag_bg_css' => 'rgba(252,211,77,0.28)',
                'tag_text_css' => '#78350f',
            ],
            [
                'slug' => 'jue',
                'nombre_dia' => 'Jueves',
                'nombre_color' => 'Naranja',
                'cereal' => 'Centeno',
                'planeta' => 'Júpiter',
                'simbolo_planeta' => '♃',
                'descripcion' => 'Día creativo',
                'blob_primary' => 'bg-orange-400/45',
                'blob_secondary' => 'bg-amber-400/35',
                'accent_text' => 'text-orange-950',
                'border_soft' => 'border-orange-200/80',
                'bg_tint_css' => 'rgba(251,146,60,0.09)',
                'border_color_css' => 'rgba(251,146,60,0.55)',
                'tag_bg_css' => 'rgba(251,146,60,0.18)',
                'tag_text_css' => '#9a3412',
            ],
            [
                'slug' => 'vie',
                'nombre_dia' => 'Viernes',
                'nombre_color' => 'Verde',
                'cereal' => 'Avena',
                'planeta' => 'Venus',
                'simbolo_planeta' => '♀',
                'descripcion' => 'Día de naturaleza y calma',
                'blob_primary' => 'bg-emerald-400/40',
                'blob_secondary' => 'bg-lime-300/35',
                'accent_text' => 'text-emerald-950',
                'border_soft' => 'border-emerald-200/80',
                'bg_tint_css' => 'rgba(52,211,153,0.09)',
                'border_color_css' => 'rgba(52,211,153,0.55)',
                'tag_bg_css' => 'rgba(52,211,153,0.16)',
                'tag_text_css' => '#065f46',
            ],
        ];
    }

    /**
     * Devuelve la info del día Waldorf para un día de la semana de Carbon (1=lun..5=vie).
     * Retorna null para sábado (6) y domingo (0).
     */
    public static function infoPorDiaSemana(int $dow): ?array
    {
        if ($dow < 1 || $dow > 5) {
            return null;
        }
        return self::diasSemana()[$dow - 1];
    }

    /**
     * Clases CSS para la celda «Día» en la agenda (lun–vie lectivo).
     * Vacío si es feriado / sin clase / fin de semana (usar entonces agenda-dia-sin-clase en la vista).
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
