<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Agenda de meriendas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Agenda de meriendas</h1>
    <p>{{ $titulo ?? 'Agenda' }}</p>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Día</th>
                <th>Cereal</th>
                <th>Fruta</th>
                <th>Elaboración</th>
                <th>Es feriado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filas as $fila)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($fila['fecha'])->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($fila['dia']) }}{{ !empty($fila['etiqueta_feriado'] ?? '') ? ' (' . $fila['etiqueta_feriado'] . ')' : '' }}</td>
                    <td>{{ $fila['cereal'] }}</td>
                    <td>{{ $fila['fruta']['nombre'] ?? '' }}</td>
                    <td>{{ $fila['elaboracion']['nombre'] ?? '' }}</td>
                    <td>{{ $fila['es_feriado'] ? 'Sí' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
