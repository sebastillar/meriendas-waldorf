@php
    $filas = $filas ?? [];
    $titulo = $titulo ?? now()->locale('es')->isoFormat('MMMM [de] YYYY');
@endphp
@extends('layouts.app')

@section('title', 'Cumpleaños – Meriendas Waldorf')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-6">
        <section class="mb-6 py-6 px-4 rounded-lg bg-[#FF4081]/10 border border-[#FF4081]/30">
            <h2 class="text-2xl font-semibold text-[#9370DB]">Cumpleaños</h2>
            <p class="text-sm text-gray-600 mt-1">Cumpleaños y familias encargadas del mes de {{ $titulo }}.</p>
        </section>

        @if (! empty($recolectaActual))
            <div class="mb-6 p-4 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-900" role="status">
                <p class="text-sm">
                    <strong>Colecta en curso:</strong>
                    regalo para <strong>{{ $recolectaActual['alumno_beneficiario']->nombre }}</strong>,
                    coordinada por la familia de <strong>{{ $recolectaActual['familia_recolectora']->nombre_para_listado }}</strong>.
                </p>
                @if (($recolectaActual['total_count'] ?? 0) > 0)
                    <p class="text-xs mt-1 text-emerald-800">
                        Aportes registrados: {{ $recolectaActual['aportaron_count'] }} / {{ $recolectaActual['total_count'] }}.
                    </p>
                @endif
            </div>
        @endif

        <div class="overflow-x-auto border border-[#CCCCFF]/50 rounded-lg bg-white">
            <table class="min-w-full divide-y divide-[#CCCCFF]/50">
                <thead class="bg-[#CCCCFF]/30">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-[#9370DB]">Niño</th>
                        <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-[#9370DB]">Fecha de cumpleaños</th>
                        <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-[#9370DB]">Familia encargada</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-[#CCCCFF]/30">
                    @forelse($filas as $fila)
                        <tr class="hover:bg-[#FFE4EC]/20">
                            <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $fila['nino'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $fila['fecha_cumpleanos'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $fila['familia_encargada'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">
                                No hay cumpleaños cargados para este mes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
