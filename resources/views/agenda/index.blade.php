@php
    /** @var \Carbon\Carbon $hoy */
    /** @var string $vista */
    /** @var int $anio */
    /** @var int $mes */
    $alumnosParaFiltro = $alumnosParaFiltro ?? [];
    $alumnoIdFiltro = $alumnoIdFiltro ?? null;
    $paramsFiltros = $paramsFiltros ?? ['vista' => 'semana'];
    $inicioSemana = $inicioSemana ?? \Carbon\Carbon::today()->startOfWeek();
    $avisosProximos = $avisosProximos ?? [];
@endphp
@extends('layouts.app')

@section('title', 'Agenda de meriendas')

@push('styles')
    <style>
        .agenda-tabla tbody tr.agenda-col-lun > td {
            background: linear-gradient(125deg, rgba(186, 230, 253, 0.72) 0%, rgba(224, 242, 254, 0.92) 52%, rgba(255, 255, 255, 0.55) 100%);
        }
        .agenda-tabla tbody tr.agenda-col-mar > td {
            background: linear-gradient(118deg, rgba(254, 205, 211, 0.68) 0%, rgba(254, 226, 226, 0.88) 50%, rgba(255, 255, 255, 0.5) 100%);
        }
        .agenda-tabla tbody tr.agenda-col-mie > td {
            background: linear-gradient(122deg, rgba(253, 230, 138, 0.7) 0%, rgba(254, 249, 195, 0.9) 52%, rgba(255, 255, 255, 0.55) 100%);
        }
        .agenda-tabla tbody tr.agenda-col-jue > td {
            background: linear-gradient(120deg, rgba(253, 186, 116, 0.65) 0%, rgba(255, 237, 213, 0.88) 52%, rgba(255, 255, 255, 0.52) 100%);
        }
        .agenda-tabla tbody tr.agenda-col-vie > td {
            background: linear-gradient(124deg, rgba(167, 243, 208, 0.68) 0%, rgba(209, 250, 229, 0.9) 52%, rgba(255, 255, 255, 0.55) 100%);
        }
    </style>
@endpush

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-6">
        {{-- Hero (solo en Agenda) --}}
        <section class="mb-6 py-6 px-4 rounded-lg bg-[#FF4081]/10 border border-[#FF4081]/30">
            <h2 class="text-2xl font-semibold text-[#9370DB]">Agenda de meriendas</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $titulo ?? '' }}</p>
        </section>

        {{-- Filtros --}}
                <div class="mb-4 p-4 bg-white border border-[#CCCCFF]/50 rounded-lg">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <h2 class="text-sm font-semibold text-[#9370DB]">Filtros</h2>
                        <a href="{{ route('agenda.public') }}" class="inline-flex items-center gap-1.5 text-sm text-[#9370DB] hover:underline" title="Ver semana actual sin filtros">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            Limpiar filtros
                        </a>
                    </div>
                    <form method="get" action="{{ route('agenda.public') }}">
                        <div class="flex flex-wrap items-end gap-4 mb-4">
                            <div>
                                <label for="filtro-nino" class="block text-xs font-medium text-gray-600 mb-1">Nombre</label>
                                <select id="filtro-nino" name="alumno_id" class="rounded border-gray-300 text-sm py-1.5 min-w-[180px]" onchange="this.form.submit()">
                                    <option value="">— Todos —</option>
                                    @foreach ($alumnosParaFiltro as $a)
                                        <option value="{{ $a['id'] }}" {{ $alumnoIdFiltro === $a['id'] ? 'selected' : '' }}>
                                            {{ $a['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-end gap-4">
                            <div>
                                <label for="filtro-periodo" class="block text-xs font-medium text-gray-600 mb-1">Período</label>
                                <select id="filtro-periodo" name="vista" class="rounded border-gray-300 text-sm py-1.5" onchange="this.form.submit()">
                                    <option value="semana" {{ $vista === 'semana' ? 'selected' : '' }}>Semana</option>
                                    <option value="mes" {{ $vista === 'mes' ? 'selected' : '' }}>Mes</option>
                                </select>
                            </div>
                            @if ($vista === 'mes')
                                <div>
                                    <label for="filtro-anio" class="block text-xs font-medium text-gray-600 mb-1">Año</label>
                                    <select id="filtro-anio" name="anio" class="rounded border-gray-300 text-sm py-1.5" onchange="this.form.submit()">
                                        @foreach (range((int) date('Y') - 1, (int) date('Y') + 1) as $y)
                                            <option value="{{ $y }}" {{ $anio === $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="filtro-mes" class="block text-xs font-medium text-gray-600 mb-1">Mes</label>
                                    <select id="filtro-mes" name="mes" class="rounded border-gray-300 text-sm py-1.5" onchange="this.form.submit()">
                                        @foreach (['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $nombreMes)
                                            <option value="{{ $i + 1 }}" {{ $mes === $i + 1 ? 'selected' : '' }}>{{ $nombreMes }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="fecha_inicio" value="{{ $inicioSemana->format('Y-m-d') }}">
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Avisos: hoy / mañana / el lunes te toca (cuando hay un niño filtrado por nombre) --}}
                @if (count($avisosProximos) > 0)
                    @foreach ($avisosProximos as $aviso)
                        <div class="mb-4 p-4 rounded-lg bg-[#CCCCFF]/30 border border-[#9370DB] text-[#4a3d6b] flex items-center gap-3" role="alert">
                            <span class="flex-shrink-0 rounded-full bg-[#9370DB]/20 p-2" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#9370DB]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0v-1a3 3 0 00-6 0v1" /></svg>
                            </span>
                            <p class="text-sm font-medium">{{ $aviso['mensaje'] }}</p>
                        </div>
                    @endforeach
                @endif

                {{-- Exportar (aplican los filtros actuales) --}}
                <div class="mb-4 p-4 bg-white border border-gray-200 rounded-lg">
                    <h2 class="text-sm font-semibold text-[#9370DB] mb-3">Exportar</h2>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('agenda.imprimir', $paramsFiltros) }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded text-sm bg-gray-200 text-gray-800 hover:bg-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2h-2m-4-1v8m-4-5v4m0-4h4" /></svg>
                            Versión para imprimir
                        </a>
                        <a href="{{ route('agenda.descargar.csv', $paramsFiltros) }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded text-sm bg-gray-200 text-gray-800 hover:bg-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            CSV
                        </a>
                        <a href="{{ route('agenda.descargar.excel', $paramsFiltros) }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded text-sm bg-emerald-200 text-emerald-800 hover:bg-emerald-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                            Excel
                        </a>
                        <a href="{{ route('agenda.descargar.pdf', $paramsFiltros) }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded text-sm bg-red-200 text-red-800 hover:bg-red-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            PDF
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto bg-white shadow rounded-lg">
                    <table class="agenda-tabla min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#CCCCFF]/30">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Día</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Cereal</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Fruta</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Elaboración</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Agregar a calendario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($filas as $fila)
                                @php
                                    $esHoy = $fila['fecha'] === now()->toDateString();
                                    $esFeriado = $fila['es_feriado'];
                                    $claseColorDia = \App\Support\ColoresDiaWaldorf::claseFilaAgenda($fila['fecha'], $esFeriado);
                                    $claseFila = $esFeriado ? 'bg-gray-300' : '';
                                    $claseFila .= $claseColorDia !== '' ? ' '.$claseColorDia : '';
                                    $claseFila .= $esHoy ? ' ring-2 ring-emerald-400' : '';
                                @endphp
                                <tr class="{{ trim($claseFila) }}">
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($fila['fecha'])->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst($fila['dia']) }}
                                        @if (!empty($fila['etiqueta_feriado'] ?? ''))
                                            <span class="block text-xs text-gray-600">{{ $fila['etiqueta_feriado'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                        {{ $fila['cereal'] }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm">
                                        @if (! empty($fila['fruta']))
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                {{ $fila['fruta']['nombre'] ?? '' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm">
                                        @if (! empty($fila['elaboracion']))
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-sky-100 text-sky-800">
                                                {{ $fila['elaboracion']['nombre'] ?? '' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm">
                                        <a href="{{ route('agenda.dia.ical', ['fecha' => $fila['fecha']]) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800" title="Agregar este día al calendario" aria-label="Agregar al calendario">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-[#9370DB]">
                    Los días grises corresponden a fines de semana o días sin clase. Los días con merienda llevan un color suave según el día de la semana
                    (<a href="{{ route('colores.por.dia') }}" class="underline hover:text-[#7b5cbf]">colores por día</a>).
                    El día de hoy aparece resaltado.
                </p>
            </div>
@endsection
