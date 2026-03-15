@php
    $filas = $filas ?? [];
    $titulo = $titulo ?? 'Agenda de meriendas';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Agenda de meriendas — Imprimir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .print\:block { display: block !important; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body class="bg-white text-gray-900">
    <div class="max-w-4xl mx-auto p-4 print:p-0">
        <h1 class="text-xl font-semibold mb-2">Agenda de meriendas</h1>
        <p class="text-sm text-gray-600 mb-4">{{ $titulo }}</p>
        <table class="min-w-full border border-gray-300 border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Fecha</th>
                    <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Día</th>
                    <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Cereal</th>
                    <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Fruta</th>
                    <th class="border border-gray-300 px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Elaboración</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($filas as $fila)
                    <tr class="{{ ($fila['es_feriado'] ?? false) ? 'bg-gray-300' : '' }}">
                        <td class="border border-gray-300 px-3 py-2 text-sm">{{ \Carbon\Carbon::parse($fila['fecha'])->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-sm">
                            {{ ucfirst($fila['dia']) }}
                            @if (!empty($fila['etiqueta_feriado'] ?? ''))
                                <span class="block text-xs text-gray-600">{{ $fila['etiqueta_feriado'] }}</span>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-sm">{{ $fila['cereal'] }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-sm">{{ $fila['fruta']['nombre'] ?? '' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-sm">{{ $fila['elaboracion']['nombre'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
