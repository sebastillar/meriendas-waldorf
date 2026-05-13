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

                {{-- Day label + date --}}
                <div class="text-center mb-7">
                    <p class="text-[0.68rem] font-semibold tracking-widest uppercase text-gray-400 mb-1">
                        {{ $proximaMerienda['etiqueta'] }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($proximaMerienda['fecha'])->locale('es')->isoFormat('dddd D [de] MMMM') }}
                    </p>
                </div>

                {{-- Cereal — protagonist --}}
                <div class="text-center mb-4">
                    <p class="cereal-hero font-bold text-gray-900 tracking-tight">
                        {{ $proximaMerienda['cereal'] }}
                    </p>
                </div>

                {{-- Planet --}}
                @if($colorDia)
                    <p class="text-center text-sm text-gray-400 mb-7 tracking-wide">
                        {{ $colorDia['simbolo_planeta'] }}&thinsp;{{ $colorDia['planeta'] }}
                    </p>
                @endif

                {{-- Separator --}}
                <hr class="border-black/10 mb-6">

                {{-- Fruta + Elaboración --}}
                <div class="space-y-3 mb-6">
                    <div class="flex items-baseline gap-2 text-sm text-gray-600">
                        <span>🍎</span>
                        <span>Fruta &mdash; <strong class="font-semibold text-gray-800">{{ $proximaMerienda['familia_fruta'] ?: '—' }}</strong></span>
                    </div>
                    <div class="flex items-baseline gap-2 text-sm text-gray-600">
                        <span>👩‍🍳</span>
                        <span>Elaboración &mdash; <strong class="font-semibold text-gray-800">{{ $proximaMerienda['familia_elaboracion'] ?: '—' }}</strong></span>
                    </div>
                </div>

                {{-- Birthday (conditional) --}}
                @if($proximoCumpleanos)
                    <hr class="border-black/10 mb-5">
                    <p class="text-xs text-gray-400">
                        🎂 Cumpleaños de
                        <strong class="font-semibold text-gray-600">{{ $proximoCumpleanos['nombre'] }}</strong>
                        &middot; {{ $proximoCumpleanos['fecha_formato'] }}
                    </p>
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
