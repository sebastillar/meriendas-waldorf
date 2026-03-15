@php
    $filas = $filas ?? [];
    $titulo = $titulo ?? now()->format('F Y');
    $diasConMerienda = $diasConMerienda ?? 0;
@endphp
@extends('layouts.app')

@section('title', 'Resumen mensual – Meriendas Waldorf')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-6">
        {{-- Hero (Resumen mensual) --}}
        <section class="mb-6 py-6 px-4 rounded-lg bg-[#FF4081]/10 border border-[#FF4081]/30">
            <h2 class="text-2xl font-semibold text-[#9370DB]">Resumen mensual</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $titulo }}</p>
        </section>

        {{-- Banner: días con merienda en el mes --}}
        <div class="mb-6 py-4 px-4 rounded-lg bg-[#CCCCFF]/30 border border-[#9370DB]/50 text-[#4a3d6b]">
            <p class="text-sm font-medium">
                En este mes hay <strong>{{ $diasConMerienda }}</strong> {{ $diasConMerienda === 1 ? 'día' : 'días' }} con merienda
                (no se cuentan fines de semana ni días sin clase).
            </p>
        </div>

        <div class="overflow-x-auto border border-[#CCCCFF]/50 rounded-lg">
            <table class="min-w-full divide-y divide-[#CCCCFF]/50">
                <thead class="bg-[#CCCCFF]/30">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-[#9370DB]">Nombre</th>
                        <th scope="col" class="px-4 py-3 text-center text-sm font-semibold text-[#9370DB]">Veces fruta</th>
                        <th scope="col" class="px-4 py-3 text-center text-sm font-semibold text-[#9370DB]">Veces elaboración</th>
                        <th scope="col" class="px-4 py-3 text-center text-sm font-semibold text-[#9370DB]">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-[#CCCCFF]/30">
                    @forelse($filas as $fila)
                        @php $total = ($fila['veces_fruta'] ?? 0) + ($fila['veces_elaboracion'] ?? 0); @endphp
                        <tr class="hover:bg-[#FFE4EC]/20">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                {{ $fila['nombre'] }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $fila['veces_fruta'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $fila['veces_elaboracion'] }}</td>
                            <td class="px-4 py-3 text-sm text-center font-medium text-gray-800">{{ $total }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">No hay alumnos activos o no hay datos para este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
