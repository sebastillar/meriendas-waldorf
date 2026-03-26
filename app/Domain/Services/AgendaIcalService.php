<?php

namespace App\Domain\Services;

/**
 * Genera iCal (.ics) y enlaces a Google Calendar a partir de filas de agenda.
 * Usa solo datos ya construidos (array de filas); no accede a repositorios ni modelos.
 */
class AgendaIcalService
{
    /**
     * Título del evento (mismo criterio que el SUMMARY del .ics).
     *
     * @param  array{fecha: string, cereal?: string, fruta?: array, elaboracion?: array, es_feriado?: bool}  $fila
     */
    public function resumenMeriendaFila(array $fila): string
    {
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
        if ($esFeriado && $summaryParts === []) {
            $summaryParts[] = 'Día sin clase';
        }

        return 'Merienda: ' . implode(', ', $summaryParts);
    }

    /**
     * URL de Google Calendar (evento de día completo) para una fila de agenda.
     *
     * @param  array{fecha: string, cereal?: string, fruta?: array, elaboracion?: array, es_feriado?: bool}  $fila
     */
    public function urlGoogleCalendarTemplate(array $fila): string
    {
        $fecha = $fila['fecha'] ?? '';
        if ($fecha === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return 'https://calendar.google.com/calendar/u/0/r';
        }

        $dtStart = str_replace('-', '', $fecha);
        $dtEnd = date('Ymd', strtotime($fecha . ' +1 day'));

        $text = $this->resumenMeriendaFila($fila);

        $params = [
            'action' => 'TEMPLATE',
            'text' => $text,
            'dates' => $dtStart . '/' . $dtEnd,
        ];

        $detailsLines = [];
        $cereal = $fila['cereal'] ?? '';
        $frutaNombre = $fila['fruta']['nombre'] ?? '';
        $elabNombre = $fila['elaboracion']['nombre'] ?? '';
        if ($cereal !== '') {
            $detailsLines[] = 'Cereal: ' . $cereal;
        }
        if ($frutaNombre !== '') {
            $detailsLines[] = 'Fruta: ' . $frutaNombre;
        }
        if ($elabNombre !== '') {
            $detailsLines[] = 'Elaboración: ' . $elabNombre;
        }
        if ($detailsLines !== []) {
            $params['details'] = implode("\n", $detailsLines);
        }

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Construye el contenido del calendario en formato RFC 5545.
     *
     * @param  array<int, array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool}>  $filas
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
            $summary = $this->resumenMeriendaFila($fila);

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
     * Sigue disponible para enlaces directos y exportaciones; la agenda pública usa {@see urlGoogleCalendarTemplate}.
     *
     * @param  array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool}  $fila
     */
    public function generarIcsUnDia(array $fila): string
    {
        $fecha = $fila['fecha'];
        $summary = $this->resumenMeriendaFila($fila);

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
