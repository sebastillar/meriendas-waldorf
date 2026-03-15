<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Colecta para regalo de cumpleaños
        </x-slot>

        @if ($recolecta)
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Colecta actual (próximo cumpleaños).</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Es para el regalo de <strong>{{ $recolecta['alumno_beneficiario_nombre'] }}</strong>.
                La colecta está coordinada por la familia de <strong>{{ $recolecta['familia_recolectora_nombre'] }}</strong>.
            </p>
            @if (isset($recolecta['monto_aportar']) && $recolecta['monto_aportar'] > 0)
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">
                    Monto a aportar a la colecta: <strong>${{ number_format($recolecta['monto_aportar'], 0, ',', '.') }}</strong>
                </p>
            @endif
            @if ($recolecta['total_count'] > 0)
                <div class="mt-4">
                    <div class="flex items-center justify-between gap-4 mb-1">
                        <span class="text-gray-600 dark:text-gray-400">Aportes a la colecta</span>
                        <span class="text-2xl font-bold tabular-nums text-gray-900 dark:text-white">{{ $recolecta['aportaron_count'] }} / {{ $recolecta['total_count'] }}</span>
                    </div>
                    <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                        <div
                            class="h-full rounded-full bg-primary-500 transition-all duration-300"
                            style="width: {{ min(100, ($recolecta['aportaron_count'] / $recolecta['total_count']) * 100) }}%"
                        ></div>
                    </div>
                </div>
            @endif
            @if (!empty($recolecta['banco']) || !empty($recolecta['numero_cuenta']) || !empty($recolecta['tipo_cuenta']) || !empty($recolecta['nombre_cuenta']) || !empty($recolecta['moneda']))
                <div class="mt-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Datos bancarios para aportes</p>
                    @if (!empty($recolecta['banco']))
                        <p class="text-sm"><span class="text-gray-500">Banco:</span> {{ $recolecta['banco'] }}</p>
                    @endif
                    @if (!empty($recolecta['tipo_cuenta']))
                        <p class="text-sm mt-1"><span class="text-gray-500">Tipo de cuenta:</span> {{ $recolecta['tipo_cuenta'] }}</p>
                    @endif
                    @if (!empty($recolecta['nombre_cuenta']))
                        <p class="text-sm mt-1"><span class="text-gray-500">Nombre de la cuenta:</span> {{ $recolecta['nombre_cuenta'] }}</p>
                    @endif
                    @if (!empty($recolecta['numero_cuenta']))
                        <p class="text-sm mt-1"><span class="text-gray-500">Número de cuenta:</span> {{ $recolecta['numero_cuenta'] }}</p>
                    @endif
                    @if (!empty($recolecta['moneda']))
                        <p class="text-sm mt-1"><span class="text-gray-500">Moneda:</span> {{ $recolecta['moneda'] }}</p>
                    @endif
                </div>
            @endif
        @else
            @if ($proximoCumpleanos ?? null)
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Próximo cumpleaños: <strong>{{ $proximoCumpleanos['nombre'] }}</strong> ({{ $proximoCumpleanos['fecha_formato'] }}).
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Para que aparezca la colecta aquí, en <strong>Familias</strong> asigne en «Regala a» la familia de {{ $proximoCumpleanos['nombre'] }} a la familia que coordinará la colecta.
                </p>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    No hay ningún alumno activo con fecha de cumpleaños cargada. Cargue fechas en <strong>Alumnos</strong> para que aparezca la colecta.
                </p>
            @endif
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
