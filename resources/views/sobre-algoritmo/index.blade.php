@extends('layouts.app')

@section('title', 'Sobre el algoritmo de asignación – Meriendas Waldorf')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-6">
        <section class="mb-8 py-6 px-4 rounded-lg bg-[#FF4081]/10 border border-[#FF4081]/30">
            <h1 class="text-2xl font-semibold text-[#9370DB]">Sobre el algoritmo de asignación</h1>
            <p class="text-sm text-gray-600 mt-1">Cómo se reparten las tareas de fruta y elaboración entre las familias.</p>
        </section>

        <div class="prose prose-purple max-w-none">
            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">¿Qué hace el algoritmo?</h2>
            <p class="text-gray-700">
                Cada día en que hay merienda (días laborables que no son feriados ni “días sin clase”), se necesitan dos familias: una lleva la <strong>fruta</strong> y otra se encarga de la <strong>elaboración</strong>. La misma familia no puede hacer las dos cosas el mismo día. El algoritmo asigna automáticamente quién hace cada tarea para repartir la carga de forma equitativa.
            </p>

            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">Días en que hay merienda</h2>
            <p class="text-gray-700">
                Solo se generan asignaciones para los <strong>días lectivos</strong>: de lunes a viernes, excluyendo los días marcados como “sin clase” (feriados, jornadas institucionales, etc.). Los fines de semana no se consideran.
            </p>

            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">Flujo general</h2>
            <p class="text-gray-700 mb-4">
                Para cada mes, el sistema recorre cada día lectivo y, en ese día, asigna primero quién lleva la fruta y después quién hace la elaboración (otra persona). Luego guarda la asignación y actualiza los conteos para el siguiente día. El siguiente diagrama resume este flujo.
            </p>
            <div class="my-6 bg-white border border-[#CCCCFF]/50 rounded-lg p-4 overflow-x-auto">
                <pre class="mermaid text-center">
flowchart TD
    A[Inicio: mes a generar] --> B[Obtener días lectivos del mes]
    B --> C[Por cada día lectivo]
    C --> D[Obtener conteos hasta ayer de cada alumno]
    D --> E[Elegir alumno para FRUTA]
    E --> F[Elegir alumno para ELABORACIÓN distinto al de fruta]
    F --> G{¿Ambos elegidos?}
    G -->|Sí| H[Guardar asignación]
    G -->|No| C
    H --> I[Actualizar conteos simulados]
    I --> C
    C --> J[Fin]
                </pre>
            </div>

            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">Criterio para elegir a una familia</h2>
            <p class="text-gray-700 mb-4">
                Para cada rol (fruta o elaboración), el sistema elige entre los alumnos activos que aún no tienen asignación ese día. La elección sigue estos pasos en orden:
            </p>
            <ol class="list-decimal list-inside text-gray-700 space-y-2">
                <li><strong>Menor cantidad de veces</strong> en ese rol hasta ese momento: se prioriza a quien menos veces ha llevado fruta (o elaborado) hasta la fecha.</li>
                <li>Si hay <strong>empate</strong>, se prioriza a quien <strong>hace más tiempo</strong> que no hacía ese rol (fecha de última vez más antigua, o nunca haberlo hecho).</li>
                <li>Si sigue el empate, se hace un <strong>desempate aleatorio</strong> determinista (misma fecha y mismo rol dan siempre el mismo resultado, para que la agenda sea reproducible).</li>
            </ol>
            <p class="text-gray-700 mt-4 mb-4">
                Así se equilibra la cantidad de veces que cada familia lleva fruta y elabora a lo largo del tiempo.
            </p>
            <div class="my-6 bg-white border border-[#CCCCFF]/50 rounded-lg p-4 overflow-x-auto">
                <pre class="mermaid text-center">
flowchart TD
    A[Candidatos: alumnos activos sin asignar ese día] --> B[Filtrar por menor conteo en el rol]
    B --> C{¿Cuántos quedan?}
    C -->|Uno| D[Elegir ese alumno]
    C -->|Varios| E[Entre ellos, filtrar por última vez más antigua]
    E --> F{¿Cuántos quedan?}
    F -->|Uno| D
    F -->|Varios| G[Desempate aleatorio determinista por fecha + rol]
    G --> D
    D --> H[Asignar al día]
                </pre>
            </div>

            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">Generación automática del mes siguiente</h2>
            <p class="text-gray-700">
                Las asignaciones del <strong>mes siguiente</strong> se recalculan y generan automáticamente el <strong>día {{ $dia_recalculo_asignaciones }} de cada mes</strong>. Ese día el sistema elimina las asignaciones ya generadas para el mes próximo y las vuelve a generar con el historial actualizado (incluidos los intercambios del mes en curso), de modo que el reparto se mantenga equitativo.
            </p>

            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">Resumen</h2>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Solo se asignan <strong>días lectivos</strong> (lun–vie, sin días sin clase).</li>
                <li>Cada día hay <strong>una familia para fruta</strong> y <strong>otra para elaboración</strong>.</li>
                <li>La elección prioriza <strong>reparto equitativo</strong> (menor cantidad de veces en ese rol y, si aplica, hace más tiempo que no lo hacía).</li>
                <li>El desempate final es <strong>aleatorio pero fijo</strong> para la misma fecha y rol.</li>
                <li>El mes siguiente se recalcula y se genera automáticamente el día {{ $dia_recalculo_asignaciones }} de cada mes.</li>
            </ul>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js" crossorigin="anonymous"></script>
        <script>mermaid.initialize({ startOnLoad: true, theme: 'neutral' });</script>
    @endpush
@endsection
