<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>@yield('title', 'Meriendas Waldorf')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-white min-h-screen flex flex-col">
        <header class="bg-[#9370DB] text-white py-4 shadow">
            <div class="max-w-5xl mx-auto px-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h1 class="text-2xl font-semibold">
                            <a href="{{ route('agenda.public') }}" class="hover:opacity-90">Meriendas Waldorf</a>
                        </h1>
                        <p class="text-sm opacity-90 text-[#FFE4EC]">Sistema de autogestión</p>
                    </div>
                    <nav class="flex items-center gap-4" aria-label="Navegación principal">
                        <a href="{{ route('agenda.public') }}" class="px-3 py-1.5 rounded text-sm font-medium {{ request()->routeIs('agenda.public') ? 'bg-white/20' : 'hover:bg-white/10' }}">Agenda</a>
                        <a href="{{ route('resumen.mensual') }}" class="px-3 py-1.5 rounded text-sm font-medium {{ request()->routeIs('resumen.mensual') ? 'bg-white/20' : 'hover:bg-white/10' }}">Resumen mensual</a>
                        <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-sm font-medium hover:bg-white/10" title="Acceder al panel de administración">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                            Ingresar
                        </a>
                    </nav>
                </div>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="bg-[#9370DB]/90 text-white py-6 mt-auto">
            <div class="max-w-5xl mx-auto px-4">
                <nav aria-label="Navegación del pie" class="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <a href="{{ route('sobre.algoritmo') }}" class="text-sm hover:underline {{ request()->routeIs('sobre.algoritmo') ? 'font-semibold' : '' }}">Sobre el algoritmo de asignación</a>
                    <a href="{{ url('/docs') }}" class="text-sm hover:underline" target="_blank" rel="noopener noreferrer">Documentación API REST</a>
                </nav>
            </div>
        </footer>
        @stack('scripts')
    </body>
</html>
