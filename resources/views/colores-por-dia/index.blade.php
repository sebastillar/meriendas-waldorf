@extends('layouts.app')

@section('title', 'Colores por día – Meriendas Waldorf')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Nunito:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .waldorf-colores {
            font-family: 'Nunito', system-ui, sans-serif;
        }
        .waldorf-colores h1, .waldorf-colores h2, .waldorf-colores .font-display {
            font-family: 'Cormorant Garamond', Georgia, serif;
        }
        .waldorf-paper {
            background-color: #faf7f2;
            background-image:
                radial-gradient(ellipse 120% 80% at 10% 0%, rgba(255, 228, 210, 0.35) 0%, transparent 50%),
                radial-gradient(ellipse 90% 60% at 90% 100%, rgba(220, 230, 240, 0.4) 0%, transparent 45%);
        }
        .watercolor-blob {
            filter: blur(48px);
            transform: rotate(-8deg);
        }
        .watercolor-blob-2 {
            filter: blur(40px);
            transform: rotate(12deg);
        }
        .card-wash {
            mix-blend-mode: multiply;
        }
    </style>
@endpush

@section('content')
    <div class="waldorf-colores waldorf-paper min-h-[70vh]">
        <div class="max-w-4xl mx-auto px-4 py-10 md:py-14">
            <header class="text-center mb-12 md:mb-16">
                <p class="text-sm font-medium tracking-wide text-[#9370DB]/90 uppercase mb-2">Ritmo de la semana</p>
                <h1 class="text-4xl md:text-5xl font-semibold text-[#5c4d7a] leading-tight">Colores por día</h1>
                <p class="mt-4 text-lg text-stone-600 max-w-2xl mx-auto leading-relaxed">
                    En muchas escuelas Waldorf, cada día de la semana se acompaña con un color que ayuda a los niños
                    a sentir el pulso del tiempo: un ritmo suave que ordena la semana sin prisa.
                    No es una regla rígida, sino un lenguaje silencioso que nutre el sentido de pertenencia al día.
                </p>
            </header>

            <div class="space-y-10 md:space-y-12">
                @foreach ($dias as $i => $dia)
                    <article
                        class="relative overflow-hidden rounded-3xl border-2 {{ $dia['border_soft'] }} bg-white/60 backdrop-blur-sm shadow-sm shadow-stone-200/50"
                        aria-labelledby="titulo-dia-{{ $dia['slug'] }}"
                    >
                        {{-- Capa acuarela --}}
                        <div class="pointer-events-none absolute inset-0 overflow-hidden rounded-3xl opacity-90" aria-hidden="true">
                            <div
                                class="watercolor-blob absolute -right-4 -top-16 h-56 w-56 md:h-72 md:w-72 rounded-full {{ $dia['blob_primary'] }} card-wash"
                            ></div>
                            <div
                                class="watercolor-blob-2 absolute -left-8 bottom-0 h-48 w-52 md:h-60 md:w-64 rounded-full {{ $dia['blob_secondary'] }} card-wash"
                            ></div>
                            <div
                                class="absolute left-1/2 top-1/2 h-32 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/30 blur-3xl"
                            ></div>
                        </div>

                        <div class="relative z-10 px-6 py-8 md:px-10 md:py-10 flex flex-col md:flex-row md:items-center md:gap-10">
                            <div class="flex-shrink-0 mb-4 md:mb-0">
                                <div
                                    class="inline-flex h-20 w-20 md:h-24 md:w-24 items-center justify-center rounded-full border-2 border-white/80 bg-white/50 shadow-inner {{ $dia['accent_text'] }}"
                                    style="box-shadow: inset 0 2px 12px rgba(255,255,255,0.9);"
                                >
                                    <span class="font-display text-3xl md:text-4xl font-semibold leading-none">{{ $i + 1 }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 id="titulo-dia-{{ $dia['slug'] }}" class="font-display text-3xl md:text-4xl font-semibold text-stone-800">
                                    {{ $dia['nombre_dia'] }}
                                </h2>
                                <p class="mt-1 text-xl md:text-2xl font-display italic {{ $dia['accent_text'] }}">
                                    {{ $dia['nombre_color'] }}
                                </p>
                                <p class="mt-4 text-stone-700 text-base md:text-lg leading-relaxed max-w-xl">
                                    {{ $dia['descripcion'] }}
                                </p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <footer class="mt-14 md:mt-16 text-center">
                <p class="text-sm text-stone-500 max-w-xl mx-auto leading-relaxed">
                    En la <a href="{{ route('agenda.public') }}" class="text-[#9370DB] font-medium hover:underline">agenda de meriendas</a>,
                    los días con clase muestran un fondo suave del color del día; los fines de semana y días sin clase se dejan neutros,
                    como un espacio en blanco en el calendario.
                </p>
            </footer>
        </div>
    </div>
@endsection
