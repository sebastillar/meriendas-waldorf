@php
    $filas = $filas ?? [];
    $titulo = $titulo ?? now()->locale('es')->isoFormat('YYYY');
    $colectasEnCurso = $colectasEnCurso ?? [];
@endphp
@extends('layouts.app')

@section('title', 'Cumpleaños – Meriendas Waldorf')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-6">
        <section class="mb-6 py-6 px-4 rounded-lg bg-[#FF4081]/10 border border-[#FF4081]/30">
            <h2 class="text-2xl font-semibold text-[#9370DB]">Cumpleaños</h2>
            <p class="text-sm text-gray-600 mt-1">Cumpleaños y familias encargadas de {{ $titulo }}.</p>
        </section>

        @if (! empty($colectasEnCurso))
            <div class="mb-6 rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-emerald-900" role="status">
                <p class="text-sm font-semibold">Colectas en curso</p>
                <div class="mt-3 space-y-2">
                    @foreach ($colectasEnCurso as $colecta)
                        <div class="rounded-md border border-emerald-200 bg-white/70 px-3 py-2 text-sm">
                            Regalo para <strong>{{ $colecta['alumno_beneficiario']->nombre }}</strong>,
                            coordinada por la familia de <strong>{{ $colecta['familia_recolectora']->nombre_para_listado }}</strong>.
                            @if (($colecta['total_count'] ?? 0) > 0)
                                <span class="text-xs text-emerald-800">
                                    (Aportes: {{ $colecta['aportaron_count'] }} / {{ $colecta['total_count'] }})
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
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
                                No hay cumpleaños cargados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
