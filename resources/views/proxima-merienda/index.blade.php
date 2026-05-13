@php
    /** @var string $modo 'dia' | 'semana' */
    /** @var string $bgTintCss */
    /** @var array|null $proximaMerienda */
    /** @var array|null $colorDia */
    /** @var array $filasProximaSemana */
    /** @var array $diasInfo */
    /** @var array|null $proximoCumpleanos */
    $proximaMerienda    = $proximaMerienda    ?? null;
    $colorDia           = $colorDia           ?? null;
    $filasProximaSemana = $filasProximaSemana ?? [];
    $diasInfo           = $diasInfo           ?? [];
    $proximoCumpleanos  = $proximoCumpleanos  ?? null;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Próxima merienda — Meriendas Waldorf</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { -webkit-font-smoothing: antialiased; }
        .cereal-hero { font-size: clamp(2.6rem, 10vw, 4rem); line-height: 1.1; }
    </style>
</head>
<body class="min-h-screen flex flex-col" style="background-color: {{ $bgTintCss }};">

    {{-- ── Header ── --}}
    <header class="px-5 py-4 flex items-center justify-between">
        <a href="{{ route('proxima.merienda') }}" class="font-semibold text-gray-800 text-sm leading-tight hover:opacity-75 transition-opacity">
            Meriendas<br class="hidden sm:inline">
            <span class="font-normal text-gray-500"> Tercer Año</span>
        </a>
        <a href="{{ route('agenda.public') }}"
           class="text-xs text-gray-400 hover:text-gray-600 transition-colors flex items-center gap-1">
            @if($modo === 'semana')
                Ver detalle&thinsp;→
            @else
                Ver agenda completa&thinsp;→
            @endif
        </a>
    </header>

    {{-- ══════════════════════════════════════════════════
         MODE: DIA
    ══════════════════════════════════════════════════ --}}
    @if($modo === 'dia')
        <main class="flex-1 flex flex-col justify-center max-w-[480px] w-full mx-auto px-5 py-8">

            @if($proximaMerienda)
                @php
                    $fechaCarbon = \Carbon\Carbon::parse($proximaMerienda['fecha']);
                    $nombreFruta  = $proximaMerienda['fruta']['nombre']       ?? '—';
                    $nombreElab   = $proximaMerienda['elaboracion']['nombre'] ?? '—';
                @endphp

                {{-- Context: etiqueta + date + planet --}}
                <div class="mb-8">
                    <p class="text-2xl font-bold text-gray-800 mb-1">
                        {{ $proximaMerienda['etiqueta'] }}
                    </p>
                    <p class="text-base text-gray-500 capitalize">
                        {{ $fechaCarbon->locale('es')->isoFormat('dddd D [de] MMMM') }}@if($colorDia)&ensp;<span class="text-gray-400">{{ $colorDia['simbolo_planeta'] }}&thinsp;{{ $colorDia['planeta'] }}</span>@endif
                    </p>
                </div>

                {{-- NAMES — first hierarchy, protagonist --}}
                <div class="grid grid-cols-2 gap-x-4 gap-y-1 mb-8">

                    {{-- Fruta --}}
                    <div>
                        <p class="text-[2rem] font-bold text-gray-900 leading-none tracking-tight break-words">
                            {{ $nombreFruta }}
                        </p>
                        <p class="text-[0.7rem] uppercase tracking-widest text-gray-400 mt-2">
                            🍎 Fruta
                        </p>
                    </div>

                    {{-- Elaboración --}}
                    <div>
                        <p class="text-[2rem] font-bold text-gray-900 leading-none tracking-tight break-words">
                            {{ $nombreElab }}
                        </p>
                        <p class="text-[0.7rem] uppercase tracking-widest text-gray-400 mt-2">
                            👩‍🍳 Elaboración
                        </p>
                        @if(($proximaMerienda['cereal'] ?? '') !== '')
                            <p class="text-sm text-gray-500 font-medium mt-0.5">
                                {{ $proximaMerienda['cereal'] }}
                            </p>
                        @endif
                    </div>

                </div>

                {{-- CTA — third hierarchy --}}
                <a href="{{ route('agenda.public') }}"
                   class="inline-flex items-center gap-2 text-xs font-semibold tracking-widest uppercase text-gray-400 hover:text-gray-700 transition-colors py-2">
                    Ver agenda semanal&thinsp;→
                </a>

                {{-- Birthday (conditional) --}}
                @if($proximoCumpleanos)
                    <div class="mt-6 pt-5 border-t border-black/10">
                        <p class="text-xs text-gray-400">
                            🎂 Cumpleaños de
                            <strong class="font-semibold text-gray-600">{{ $proximoCumpleanos['nombre'] }}</strong>
                            &middot; {{ $proximoCumpleanos['fecha_formato'] }}
                        </p>
                    </div>
                @endif

            @else
                <p class="text-center text-gray-400 text-sm py-16">
                    No hay merienda programada próximamente.
                </p>
            @endif

        </main>

    {{-- ══════════════════════════════════════════════════
         MODE: SEMANA (domingo)
    ══════════════════════════════════════════════════ --}}
    @else
        <main class="flex-1 max-w-[520px] w-full mx-auto px-5 py-8">

            {{-- Section label --}}
            <p class="text-[0.68rem] font-semibold tracking-widest uppercase text-gray-400 mb-5">
                Esta semana
            </p>

            @if(count($filasProximaSemana) > 0)
                <div class="space-y-0">
                    @foreach($filasProximaSemana as $fila)
                        @php
                            $dow = \Carbon\Carbon::parse($fila['fecha'])->dayOfWeek;
                            // 1=lun..5=vie; diasInfo is 0-indexed
                            $diaInfo = (!$fila['es_feriado'] && $dow >= 1 && $dow <= 5)
                                ? ($diasInfo[$dow - 1] ?? null)
                                : null;
                            $borderCss = $diaInfo ? $diaInfo['border_color_css'] : 'rgba(0,0,0,0.08)';
                            $fechaLabel = \Carbon\Carbon::parse($fila['fecha'])->locale('es')->isoFormat('ddd D');
                        @endphp

                        <div class="py-3 border-b border-black/5 border-l-[3px] pl-3 last:border-b-0"
                             style="border-left-color: {{ $borderCss }}; @if($fila['es_feriado']) opacity: 0.45; @endif">

                            {{-- Row top: day + date left, cereal right --}}
                            <div class="flex items-baseline justify-between gap-2">
                                <span class="text-xs font-medium text-gray-500 capitalize">{{ $fechaLabel }}</span>
                                @if(!$fila['es_feriado'] && ($fila['cereal'] ?? '') !== '')
                                    <span class="text-sm font-semibold text-gray-800">{{ $fila['cereal'] }}</span>
                                @elseif($fila['es_feriado'])
                                    <span class="text-xs italic text-gray-400">Sin clase</span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </div>

                            {{-- Row bottom: families --}}
                            @if(!$fila['es_feriado'] && ($fila['cereal'] ?? '') !== '')
                                <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-1">
                                    <span class="text-[0.7rem] text-gray-400">
                                        🍎 <span class="text-gray-500">{{ $fila['fruta']['familia_nombre'] ?? ($fila['fruta']['nombre'] ?? '—') }}</span>
                                    </span>
                                    <span class="text-[0.7rem] text-gray-400">
                                        👩‍🍳 <span class="text-gray-500">{{ $fila['elaboracion']['familia_nombre'] ?? ($fila['elaboracion']['nombre'] ?? '—') }}</span>
                                    </span>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 py-8">No hay meriendas programadas para la próxima semana.</p>
            @endif

            {{-- Birthday (conditional) --}}
            @if($proximoCumpleanos)
                <div class="mt-7 pt-5 border-t border-black/5">
                    <p class="text-xs text-gray-400">
                        🎂 Cumpleaños de
                        <strong class="font-semibold text-gray-600">{{ $proximoCumpleanos['nombre'] }}</strong>
                        &middot; {{ $proximoCumpleanos['fecha_formato'] }}
                    </p>
                </div>
            @endif

        </main>
    @endif

    {{-- ── Footer ── --}}
    <footer class="mt-auto py-5 border-t border-black/5">
        <nav class="flex flex-wrap justify-center gap-x-5 gap-y-1" aria-label="Navegación">
            <a href="{{ route('agenda.public') }}"
               class="text-xs text-gray-400 hover:text-gray-600 transition-colors">Agenda semanal</a>
            <a href="{{ route('resumen.mensual') }}"
               class="text-xs text-gray-400 hover:text-gray-600 transition-colors">Resumen mensual</a>
            <a href="{{ route('cumpleanos.index') }}"
               class="text-xs text-gray-400 hover:text-gray-600 transition-colors">Cumpleaños</a>
        </nav>
    </footer>

</body>
</html>
