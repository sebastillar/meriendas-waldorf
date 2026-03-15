<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AgendaExport implements FromArray, WithHeadings
{
    /**
     * @param array<int, array{fecha: string, dia: string, cereal: string, fruta: array, elaboracion: array, es_feriado: bool}> $filas
     */
    public function __construct(
        private array $filas
    ) {}

    public function headings(): array
    {
        return ['Fecha', 'Día', 'Cereal', 'Fruta', 'Elaboración', 'Es feriado'];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->filas as $row) {
            $rows[] = [
                $row['fecha'],
                $row['dia'],
                $row['cereal'],
                $row['fruta']['nombre'] ?? '',
                $row['elaboracion']['nombre'] ?? '',
                $row['es_feriado'] ? 'Sí' : 'No',
            ];
        }
        return $rows;
    }
}
