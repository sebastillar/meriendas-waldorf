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
                Cada día en que hay merienda (días laborables que no son feriados ni “días sin clase”), se necesitan dos familias: una lleva la <strong>fruta</strong> y otra se encarga de la <strong>elaboración</strong>. La misma familia no puede hacer las dos cosas el mismo día. El algoritmo asigna automáticamente quién hace cada tarea para repartir la carga de forma equitativa <strong>dentro del mes que se genera</strong>, sin usar cuántas veces llevó fruta o elaboró en meses anteriores.
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
    C --> D[Conteos solo del mes en curso acumulados día a día]
    D --> E[Elegir alumno para FRUTA]
    E --> F[Elegir alumno para ELABORACIÓN distinto al de fruta]
    F --> G{¿Ambos elegidos?}
    G -->|Sí| H[Guardar asignación]
    G -->|No| C
    H --> I[Actualizar conteos simulados y semanales]
    I --> C
    C --> J[Fin]
                </pre>
            </div>

            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">Reparto dentro de la semana (una aparición por familia)</h2>
            <p class="text-gray-700 mb-4">
                La <strong>semana</strong> se considera de <strong>lunes a domingo</strong>. Se cuenta cuántas veces cada familia ya salió en la agenda esa semana <strong>en total</strong> (fruta más elaboración). Primero se favorece a quien tiene <strong>menos apariciones en la semana</strong>, así en la práctica cada familia entra como mucho <strong>un día por semana</strong> mientras haya otras que aún no hayan tocado. Solo cuando ya no queda nadie con menos apariciones semanales (por ejemplo, hay más días lectivos que familias en esa semana) puede repetirse alguien, y entonces siguen aplicándose el resto de criterios.
            </p>
            <p class="text-gray-700 mb-4">
                Si una semana <strong>cruza dos meses</strong> y se regenera el calendario a partir de un día a mitad de semana, las asignaciones <strong>anteriores</strong> a ese día (aunque sigan en el mismo lunes–domingo) cuentan para esos conteos semanales.
            </p>

            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">Descanso entre días lectivos seguidos</h2>
            <p class="text-gray-700 mb-4">
                Entre dos <strong>días lectivos consecutivos</strong> (sin contar fines de semana ni días sin clase entre medio), una familia <strong>no</strong> pasa de elaborar un día a llevar fruta al siguiente, ni al revés: quien elaboró el último día lectivo no puede llevar fruta al día lectivo siguiente, y quien llevó fruta no puede elaborar al día lectivo siguiente. Se usa la asignación ya guardada del día lectivo anterior (aunque sea de otro mes si la semana cruza el corte).
            </p>
            <p class="text-gray-700 mb-4">
                Si con esa regla no hubiera ningún candidato posible (por ejemplo, muy pocos alumnos activos), el sistema <strong>relaja solo esta regla</strong> ese día para poder cerrar la fila y deja constancia en el registro de la aplicación.
            </p>

            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">Criterio para elegir a una familia</h2>
            <p class="text-gray-700 mb-4">
                Para cada rol (fruta o elaboración), primero se excluyen quienes no pueden tocar ese rol (la otra familia el mismo día, y la regla del día lectivo anterior si aplica). Entre los candidatos restantes, la elección sigue estos pasos en orden:
            </p>
            <ol class="list-decimal list-inside text-gray-700 space-y-2">
                <li><strong>Menor cantidad de apariciones en la semana</strong> (lunes–domingo), sumando fruta y elaboración: se prioriza a quien menos veces ha salido en la agenda esa semana en cualquier rol.</li>
                <li><strong>Menor cantidad de apariciones en total en el mes</strong> que se está generando (fruta + elaboración; sin usar meses anteriores), para repartir mejor los turnos entre familias.</li>
                <li><strong>Equilibrio entre fruta y elaboración</strong> para esa familia: al elegir fruta, favorece a quien tiene más elaboraciones que frutas; al elegir elaboración, a quien tiene más frutas que elaboraciones.</li>
                <li><strong>Menor cantidad de veces en ese rol en el mes</strong> (solo lo acumulado desde el primer día regenerado en adelante), entre quienes empatan en lo anterior.</li>
                <li>Después se prioriza a quien <strong>hace más tiempo</strong> que no hacía ese rol (fecha de última vez más antigua, o nunca haberlo hecho).</li>
                <li>Si sigue el empate, <strong>desempate aleatorio</strong> determinista (misma fecha y mismo rol dan siempre el mismo resultado).</li>
            </ol>
            <p class="text-gray-700 mt-4 mb-4">
                Así se combina a lo sumo un turno por semana por familia (mientras el cupo lo permita), reparto parejo de turnos en el mes, equilibrio fruta/elaboración y el conteo por rol dentro del mes.
            </p>
            <div class="my-6 bg-white border border-[#CCCCFF]/50 rounded-lg p-4 overflow-x-auto">
                <pre class="mermaid text-center">
flowchart TD
    A[Candidatos: alumnos activos sin asignar ese día] --> B[Filtrar por menor apariciones en la semana fruta+elab]
    B --> B2[Filtrar por menor apariciones en el mes fruta+elab]
    B2 --> C[Filtrar por mayor equilibrio cruzado fruta/elaboración]
    C --> D[Filtrar por menor conteo de ese rol en el mes]
    D --> E{¿Cuántos quedan?}
    E -->|Uno| F[Elegir ese alumno]
    E -->|Varios| G[Entre ellos, filtrar por última vez más antigua]
    G --> H{¿Cuántos quedan?}
    H -->|Uno| F
    H -->|Varios| I[Desempate aleatorio determinista por fecha + rol]
    I --> F
    F --> J[Asignar al día]
                </pre>
            </div>

            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">Generación automática del mes siguiente</h2>
            <p class="text-gray-700">
                Las asignaciones del <strong>mes siguiente</strong> se recalculan y generan automáticamente el <strong>día {{ $dia_recalculo_asignaciones }} de cada mes</strong>. Ese día el sistema elimina las asignaciones ya generadas para el mes próximo y las vuelve a generar: el reparto equitativo de fruta y elaboración se calcula <strong>solo dentro de ese mes</strong> (no arrastra conteos de meses viejos). Los intercambios u otros cambios del mes en curso siguen reflejados en la agenda, pero cada mes nuevo parte de cero para esos totales.
            </p>

            <h2 class="text-xl font-semibold text-[#9370DB] mt-8 mb-3">Resumen</h2>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Solo se asignan <strong>días lectivos</strong> (lun–vie, sin días sin clase).</li>
                <li>Cada día hay <strong>una familia para fruta</strong> y <strong>otra para elaboración</strong>.</li>
                <li>Dentro de cada semana (lun–dom) se prioriza <strong>no repetir familia</strong> hasta que el resto haya tenido al menos un turno esa semana (fruta + elaboración cuentan juntos).</li>
                <li>No se alterna <strong>fruta y elaboración en dos días lectivos seguidos</strong> para la misma familia, salvo relajación por falta de candidatos.</li>
                <li>Tras la semana, se prioriza <strong>equilibrio fruta vs elaboración</strong>, luego el conteo del rol <strong>solo en el mes generado</strong> y la antigüedad en ese rol.</li>
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
