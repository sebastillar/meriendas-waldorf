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
    $estadisticasMes = $estadisticasMes ?? null;
    $urlsGoogleCalendarPorFecha = $urlsGoogleCalendarPorFecha ?? [];
@endphp
@extends('layouts.app')

@section('title', 'Agenda de meriendas')

@push('styles')
    <style>
        /* Color Waldorf solo en la columna «Día»; textos oscuros para contraste (WCAG) */
        .agenda-tabla tbody td.agenda-dia-celda.agenda-col-lun {
            background: linear-gradient(145deg, rgba(125, 211, 252, 0.55) 0%, rgba(186, 230, 253, 0.85) 45%, rgba(224, 242, 254, 0.95) 100%);
            color: #0c2e44;
            font-weight: 600;
            border-left: 3px solid #0284c7;
        }
        .agenda-tabla tbody td.agenda-dia-celda.agenda-col-mar {
            background: linear-gradient(140deg, rgba(251, 113, 133, 0.45) 0%, rgba(254, 205, 211, 0.82) 50%, rgba(255, 228, 230, 0.95) 100%);
            color: #4c0519;
            font-weight: 600;
            border-left: 3px solid #e11d48;
        }
        .agenda-tabla tbody td.agenda-dia-celda.agenda-col-mie {
            background: linear-gradient(140deg, rgba(250, 204, 21, 0.5) 0%, rgba(253, 230, 138, 0.88) 50%, rgba(254, 249, 195, 0.98) 100%);
            color: #422006;
            font-weight: 600;
            border-left: 3px solid #ca8a04;
        }
        .agenda-tabla tbody td.agenda-dia-celda.agenda-col-jue {
            background: linear-gradient(138deg, rgba(251, 146, 60, 0.48) 0%, rgba(253, 186, 116, 0.82) 48%, rgba(255, 237, 213, 0.96) 100%);
            color: #431407;
            font-weight: 600;
            border-left: 3px solid #ea580c;
        }
        .agenda-tabla tbody td.agenda-dia-celda.agenda-col-vie {
            background: linear-gradient(142deg, rgba(52, 211, 153, 0.45) 0%, rgba(167, 243, 208, 0.85) 50%, rgba(209, 250, 229, 0.98) 100%);
            color: #022c22;
            font-weight: 600;
            border-left: 3px solid #059669;
        }
        .agenda-tabla tbody td.agenda-dia-celda.agenda-dia-sin-clase {
            background: #e2e8f0;
            color: #334155;
            font-weight: 500;
            border-left: 3px solid #94a3b8;
        }
        /* Una sola franja «hoy» en el borde izquierdo de la fila (primera celda) */
        .agenda-tabla tbody tr.agenda-fila-hoy > th:first-child {
            box-shadow: inset 4px 0 0 0 #059669;
        }
        details.agenda-mes-stats > summary {
            list-style: none;
        }
        details.agenda-mes-stats > summary::-webkit-details-marker {
            display: none;
        }
        details.agenda-mes-stats[open] > summary .agenda-mes-stats-chevron {
            transform: rotate(180deg);
        }
        /* Móvil: color Waldorf en la celda fecha (columna «Día» oculta) */
        @media (max-width: 639px) {
            .agenda-tabla .agenda-fecha-celda.agenda-col-lun {
                border-left: 4px solid #0284c7;
                background: linear-gradient(90deg, rgba(186, 230, 253, 0.4) 0%, transparent 72%);
            }
            .agenda-tabla .agenda-fecha-celda.agenda-col-mar {
                border-left: 4px solid #e11d48;
                background: linear-gradient(90deg, rgba(254, 205, 211, 0.45) 0%, transparent 72%);
            }
            .agenda-tabla .agenda-fecha-celda.agenda-col-mie {
                border-left: 4px solid #ca8a04;
                background: linear-gradient(90deg, rgba(253, 230, 138, 0.45) 0%, transparent 72%);
            }
            .agenda-tabla .agenda-fecha-celda.agenda-col-jue {
                border-left: 4px solid #ea580c;
                background: linear-gradient(90deg, rgba(253, 186, 116, 0.42) 0%, transparent 72%);
            }
            .agenda-tabla .agenda-fecha-celda.agenda-col-vie {
                border-left: 4px solid #059669;
                background: linear-gradient(90deg, rgba(167, 243, 208, 0.42) 0%, transparent 72%);
            }
            .agenda-tabla .agenda-fecha-celda.agenda-dia-sin-clase {
                border-left: 4px solid #94a3b8;
                background: linear-gradient(90deg, rgba(226, 232, 240, 0.9) 0%, transparent 72%);
            }
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

                @if ($vista === 'mes' && $estadisticasMes !== null && count($estadisticasMes['filas']) > 0)
                    <details class="agenda-mes-stats mb-4 rounded-lg border border-[#CCCCFF]/60 bg-white shadow-sm ring-1 ring-violet-900/5">
                        <summary class="flex cursor-pointer items-center justify-between gap-3 rounded-lg px-4 py-3 text-[#9370DB] outline-none hover:bg-violet-50/60 focus-visible:ring-2 focus-visible:ring-[#9370DB]/40">
                            <span class="font-semibold">Ver estadísticas del mes</span>
                            <svg class="agenda-mes-stats-chevron h-5 w-5 shrink-0 text-[#9370DB] transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="border-t border-slate-100 px-4 pb-4 pt-3">
                            <h2 class="sr-only">Estadísticas del mes</h2>
                            @if (! empty($estadisticasMes['periodo_etiqueta']))
                                <p class="text-xs text-gray-600 mb-3">
                                    Periodo con planificación de meriendas: <strong class="font-medium text-slate-800">{{ $estadisticasMes['periodo_etiqueta'] }}</strong>
                                    ({{ $estadisticasMes['dias_con_plan'] }} {{ $estadisticasMes['dias_con_plan'] === 1 ? 'día' : 'días' }} con asignación).
                                </p>
                            @endif
                            <div class="overflow-x-auto rounded-lg border border-slate-200">
                                <table class="min-w-full text-sm text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-100 text-slate-700 border-b border-slate-200">
                                            <th scope="col" class="px-3 py-2 font-semibold">Alumno</th>
                                            <th scope="col" class="px-3 py-2 font-semibold text-center tabular-nums">Elaboración</th>
                                            <th scope="col" class="px-3 py-2 font-semibold text-center tabular-nums">Fruta</th>
                                            <th scope="col" class="px-3 py-2 font-semibold text-center tabular-nums">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($estadisticasMes['filas'] as $filaStat)
                                            <tr class="hover:bg-violet-50/40">
                                                <td class="px-3 py-2 font-medium text-slate-900">{{ $filaStat['nombre'] }}</td>
                                                <td class="px-3 py-2 text-center tabular-nums text-sky-900">{{ $filaStat['elaboracion'] }}</td>
                                                <td class="px-3 py-2 text-center tabular-nums text-emerald-900">{{ $filaStat['fruta'] }}</td>
                                                <td class="px-3 py-2 text-center tabular-nums text-slate-700">{{ $filaStat['total'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-3 text-xs leading-relaxed text-slate-600">
                                Los números rara vez son idénticos para todos: cada día hay <strong>dos cupos distintos</strong> (fruta y elaboración) y el algoritmo intenta a la vez repartir turnos en el mes, no repetir a la misma familia en la misma semana cuando hay margen, equilibrar fruta vs elaboración por familia y evitar el mismo rol en dos días lectivos seguidos. Eso deja empates que se resuelven con reglas de prioridad y un desempate fijo por fecha, así que es normal ver diferencias de 1–2 turnos entre alumnos si el mes no divide en partes exactas entre todos.
                            </p>
                        </div>
                    </details>
                @endif

                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-900/5 -mx-1 px-1 sm:mx-0 sm:px-0">
                    <table class="agenda-tabla min-w-0 w-full max-w-full border-collapse text-left text-xs sm:min-w-full sm:text-sm">
                        <caption class="sr-only">Agenda de meriendas: fecha, día, cereal, responsables de fruta y elaboración, enlace para añadir el día en Google Calendar</caption>
                        <thead>
                            <tr class="border-b border-white/20 bg-[#9370DB] text-white">
                                <th scope="col" class="px-2 py-2.5 text-[0.65rem] font-semibold uppercase tracking-wide text-white sm:px-5 sm:py-3.5 sm:text-xs sm:tracking-wider">Fecha</th>
                                <th scope="col" class="hidden px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-white sm:table-cell sm:px-5">Día</th>
                                <th scope="col" class="px-2 py-2.5 text-[0.65rem] font-semibold uppercase tracking-wide text-white sm:px-5 sm:py-3.5 sm:text-xs sm:tracking-wider">Cereal</th>
                                <th scope="col" class="px-2 py-2.5 text-[0.65rem] font-semibold uppercase tracking-wide text-white sm:hidden min-w-[7rem]">Merienda</th>
                                <th scope="col" class="hidden px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-white sm:table-cell sm:px-5">Fruta</th>
                                <th scope="col" class="hidden px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-white sm:table-cell sm:px-5">Elaboración</th>
                                <th scope="col" class="w-px whitespace-nowrap px-1 py-2.5 text-center text-white sm:w-auto sm:px-5 sm:py-3.5 sm:text-left">
                                    <span class="hidden sm:inline text-xs font-semibold uppercase tracking-wider">Google Calendar</span>
                                    <span class="inline-flex justify-center sm:hidden" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white opacity-95" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </span>
                                    <span class="sr-only sm:hidden">Añadir en Google Calendar</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($filas as $index => $fila)
                                @php
                                    $esHoy = $fila['fecha'] === now()->toDateString();
                                    $esFeriado = $fila['es_feriado'];
                                    $claseColorDia = \App\Support\ColoresDiaWaldorf::claseFilaAgenda($fila['fecha'], $esFeriado);
                                    $claseCeldaDia = $esFeriado ? 'agenda-dia-sin-clase' : $claseColorDia;
                                    $claseFila = $esFeriado ? 'bg-slate-200/90' : ($index % 2 === 1 ? 'bg-slate-50/80' : 'bg-white');
                                    $claseFila .= $esHoy ? ' agenda-fila-hoy' : '';
                                    $fechaCarbon = \Carbon\Carbon::parse($fila['fecha'])->locale('es');
                                    $diaAbrev = $esFeriado ? '' : $fechaCarbon->isoFormat('ddd');
                                @endphp
                                <tr class="{{ trim($claseFila) }} transition-colors hover:bg-violet-50/50 focus-within:bg-violet-50/40" @if ($esHoy) aria-current="date" @endif>
                                    <th scope="row" class="agenda-fecha-celda {{ $claseCeldaDia }} px-2 py-2 align-top font-medium tabular-nums text-slate-900 sm:bg-transparent sm:px-5 sm:py-3.5">
                                        <span class="block leading-tight">{{ $fechaCarbon->format('d/m/Y') }}</span>
                                        @if ($diaAbrev !== '')
                                            <span class="mt-0.5 block text-[0.7rem] font-semibold capitalize leading-tight text-slate-800 sm:hidden">{{ $diaAbrev }}</span>
                                        @endif
                                        @if ($esHoy)
                                            <span class="mt-1 inline-flex rounded-full bg-emerald-600 px-1.5 py-0.5 text-[0.6rem] font-semibold uppercase tracking-wide text-white sm:px-2 sm:text-[0.65rem]">Hoy</span>
                                        @endif
                                    </th>
                                    <td class="agenda-dia-celda {{ $claseCeldaDia }} hidden px-4 py-3 align-top text-sm leading-snug sm:table-cell sm:px-5 sm:py-3.5">
                                        <span class="block">{{ ucfirst($fila['dia']) }}</span>
                                        @if (! empty($fila['etiqueta_feriado'] ?? ''))
                                            <span class="mt-1 block text-xs font-normal opacity-90">{{ $fila['etiqueta_feriado'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 align-top text-slate-800 sm:px-5 sm:py-3.5 sm:text-sm">
                                        @if ($esFeriado)
                                            <span class="text-slate-500">—</span>
                                        @else
                                            <span class="font-medium leading-snug text-slate-900">{{ $fila['cereal'] ?: '—' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 align-top sm:hidden">
                                        @if ($esFeriado)
                                            @if (! empty($fila['etiqueta_feriado'] ?? ''))
                                                <span class="text-xs text-slate-600">{{ $fila['etiqueta_feriado'] }}</span>
                                            @else
                                                <span class="text-slate-500">—</span>
                                            @endif
                                        @else
                                            <div class="flex flex-col gap-1.5">
                                                <div>
                                                    <span class="mb-0.5 block text-[0.65rem] font-semibold uppercase tracking-wide text-emerald-800">Fruta</span>
                                                    @if (! empty($fila['fruta']))
                                                        <span class="inline-flex max-w-full items-center rounded-md border border-emerald-200/80 bg-emerald-50 px-2 py-0.5 text-[0.7rem] font-medium leading-snug text-emerald-900">
                                                            {{ $fila['fruta']['nombre'] ?? '' }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400">—</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="mb-0.5 block text-[0.65rem] font-semibold uppercase tracking-wide text-sky-900">Elaboración</span>
                                                    @if (! empty($fila['elaboracion']))
                                                        <span class="inline-flex max-w-full items-center rounded-md border border-sky-200/80 bg-sky-50 px-2 py-0.5 text-[0.7rem] font-medium leading-snug text-sky-950">
                                                            {{ $fila['elaboracion']['nombre'] ?? '' }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400">—</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="hidden px-4 py-3 align-top sm:table-cell sm:px-5 sm:py-3.5 sm:text-sm">
                                        @if (! empty($fila['fruta']))
                                            <span class="inline-flex max-w-full items-center rounded-lg border border-emerald-200/80 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-900">
                                                {{ $fila['fruta']['nombre'] ?? '' }}
                                            </span>
                                        @elseif (! $esFeriado)
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="hidden px-4 py-3 align-top sm:table-cell sm:px-5 sm:py-3.5 sm:text-sm">
                                        @if (! empty($fila['elaboracion']))
                                            <span class="inline-flex max-w-full items-center rounded-lg border border-sky-200/80 bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-950">
                                                {{ $fila['elaboracion']['nombre'] ?? '' }}
                                            </span>
                                        @elseif (! $esFeriado)
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-1 py-2 align-top sm:px-5 sm:py-3.5">
                                        <a
                                            href="{{ $urlsGoogleCalendarPorFecha[$fila['fecha']] ?? 'https://calendar.google.com/calendar/u/0/r' }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center rounded-lg p-1.5 text-indigo-700 outline-none ring-indigo-400 hover:bg-indigo-50 hover:text-indigo-900 focus-visible:ring-2 sm:gap-2 sm:px-2 sm:py-1.5 sm:text-sm sm:font-medium sm:underline sm:decoration-indigo-300 sm:underline-offset-2"
                                            title="Abrir Google Calendar para añadir este día (merienda)"
                                            aria-label="Añadir en Google Calendar el día {{ $fechaCarbon->format('d/m/Y') }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-indigo-600 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            <span class="hidden sm:inline">Añadir</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-600">
                    En el móvil, la tabla muestra <strong>fecha</strong> (día abreviado y color Waldorf en el borde), <strong>cereal</strong>, <strong>merienda</strong> (fruta y elaboración) y un enlace a <strong>Google Calendar</strong> para crear el evento del día (se abre en una pestaña nueva; hace falta cuenta de Google). La exportación <strong>iCal (.ics)</strong> del bloque de arriba sigue sirviendo para otros calendarios. En pantalla grande aparece la columna <strong>Día</strong> completa
                    (<a href="{{ route('colores.por.dia') }}" class="font-medium text-[#9370DB] underline decoration-violet-300 underline-offset-2 hover:text-[#7b5cbf]">colores por día</a>).
                    La fila de <strong>hoy</strong> lleva franja verde a la izquierda y la etiqueta «Hoy».
                </p>
            </div>
@endsection
