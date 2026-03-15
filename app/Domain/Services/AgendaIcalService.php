<?php

namespace App\Domain\Services;

/**
 * Genera un archivo iCal (.ics) a partir de las filas de agenda.
 * Usa solo datos ya construidos (array de filas); no accede a repositorios ni modelos.
 */
class AgendaIcalService
{
    /**
     * Construye el contenido del calendario en formato RFC 5545.
     *
     * @param array<int, array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool}> $filas
     */
    public function generarIcs(array $filas): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Meriendas Waldorf//Agenda//ES',
            'CALSCALE:GREGORIAN',
        ];

        foreach ($filas as $fila) {
            $fecha = $fila['fecha'];
            $cereal = $fila['cereal'] ?? '';
            $frutaNombre = $fila['fruta']['nombre'] ?? '';
            $elabNombre = $fila['elaboracion']['nombre'] ?? '';
            $esFeriado = $fila['es_feriado'] ?? false;

            $summaryParts = [];
            if ($cereal !== '') {
                $summaryParts[] = $cereal;
            }
            if ($frutaNombre !== '') {
                $summaryParts[] = 'fruta ' . $frutaNombre;
            }
            if ($elabNombre !== '') {
                $summaryParts[] = 'elaboración ' . $elabNombre;
            }
            if ($esFeriado && empty($summaryParts)) {
                $summaryParts[] = 'Día sin clase';
            }
            $summary = 'Merienda: ' . implode(', ', $summaryParts);

            $dtStart = str_replace('-', '', $fecha);
            $dtEnd = date('Ymd', strtotime($fecha . ' +1 day'));

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'DTSTART;VALUE=DATE:' . $dtStart;
            $lines[] = 'DTEND;VALUE=DATE:' . $dtEnd;
            $lines[] = 'SUMMARY:' . $this->escapeIcalString($summary);
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';
        return implode("\r\n", $lines);
    }

    /**
     * Genera un .ics con un solo evento para un día (una fila de agenda).
     * Para enlace "Añadir al calendario" por cada día en la tabla.
     *
     * @param array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool} $fila
     */
    public function generarIcsUnDia(array $fila): string
    {
        $fecha = $fila['fecha'];
        $cereal = $fila['cereal'] ?? '';
        $frutaNombre = $fila['fruta']['nombre'] ?? '';
        $elabNombre = $fila['elaboracion']['nombre'] ?? '';
        $esFeriado = $fila['es_feriado'] ?? false;

        $summaryParts = [];
        if ($cereal !== '') {
            $summaryParts[] = $cereal;
        }
        if ($frutaNombre !== '') {
            $summaryParts[] = 'fruta ' . $frutaNombre;
        }
        if ($elabNombre !== '') {
            $summaryParts[] = 'elaboración ' . $elabNombre;
        }
        if ($esFeriado && empty($summaryParts)) {
            $summaryParts[] = 'Día sin clase';
        }
        $summary = 'Merienda: ' . implode(', ', $summaryParts);

        $dtStart = str_replace('-', '', $fecha);
        $dtEnd = date('Ymd', strtotime($fecha . ' +1 day'));

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Meriendas Waldorf//Agenda//ES',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            'DTSTART;VALUE=DATE:' . $dtStart,
            'DTEND;VALUE=DATE:' . $dtEnd,
            'SUMMARY:' . $this->escapeIcalString($summary),
            'END:VEVENT',
            'END:VCALENDAR',
        ];
        return implode("\r\n", $lines);
    }

    private function escapeIcalString(string $s): string
    {
        $s = str_replace(['\\', ';', ',', "\n"], ['\\\\', '\\;', '\\,', '\\n'], $s);
        return $s;
    }
}
