<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Colecta para regalo de cumpleaños
        </x-slot>

        @if (!empty($recolectasMes))
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Colectas del mes actual.</p>

            <div class="mt-3 space-y-4">
                @foreach ($recolectasMes as $item)
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Regalo de <strong>{{ $item['alumno_beneficiario_nombre'] }}</strong>
                                    <span class="text-gray-500">({{ $item['fecha_formato'] }})</span>
                                </p>
                            </div>

                            <span class="text-xs font-medium px-2 py-1 rounded-full
                                {{ $item['estado'] === 'activa'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
                                }}">
                                {{ $item['estado'] === 'activa' ? 'Activa' : 'Sin recolectora' }}
                            </span>
                        </div>

                        @if ($item['estado'] === 'activa' && isset($item['familia_recolectora_nombre']))
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                Coordinada por la familia de <strong>{{ $item['familia_recolectora_nombre'] }}</strong>.
                            </p>
                        @endif

                        @if ($item['estado'] === 'sin_recolectora')
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                Para que aparezca la colecta aquí, en <strong>Familias</strong> asigne en «Regala a» la familia del cumpleañero a la familia que coordinará la colecta.
                            </p>
                        @endif

                        @if (isset($item['monto_aportar']) && $item['monto_aportar'] > 0 && $item['estado'] === 'activa')
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-3">
                                Monto a aportar a la colecta: <strong>${{ number_format($item['monto_aportar'], 0, ',', '.') }}</strong>
                            </p>
                        @endif

                        @if (($item['total_count'] ?? 0) > 0)
                            <div class="mt-4">
                                <div class="flex items-center justify-between gap-4 mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Aportes a la colecta</span>
                                    <span class="text-2xl font-bold tabular-nums text-gray-900 dark:text-white">{{ $item['aportaron_count'] }} / {{ $item['total_count'] }}</span>
                                </div>
                                <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                    <div
                                        class="h-full rounded-full bg-primary-500 transition-all duration-300"
                                        style="width: {{ min(100, ($item['aportaron_count'] / $item['total_count']) * 100) }}%"
                                    ></div>
                                </div>
                            </div>
                        @endif

                        @if ($item['estado'] === 'activa' && (!empty($item['banco']) || !empty($item['numero_cuenta']) || !empty($item['tipo_cuenta']) || !empty($item['nombre_cuenta']) || !empty($item['moneda'])))
                            <div class="mt-4 p-4 rounded-lg bg-white/70 dark:bg-gray-900/20">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Datos bancarios para aportes</p>
                                @if (!empty($item['banco']))
                                    <p class="text-sm"><span class="text-gray-500">Banco:</span> {{ $item['banco'] }}</p>
                                @endif
                                @if (!empty($item['tipo_cuenta']))
                                    <p class="text-sm mt-1"><span class="text-gray-500">Tipo de cuenta:</span> {{ $item['tipo_cuenta'] }}</p>
                                @endif
                                @if (!empty($item['nombre_cuenta']))
                                    <p class="text-sm mt-1"><span class="text-gray-500">Nombre de la cuenta:</span> {{ $item['nombre_cuenta'] }}</p>
                                @endif
                                @if (!empty($item['numero_cuenta']))
                                    <p class="text-sm mt-1"><span class="text-gray-500">Número de cuenta:</span> {{ $item['numero_cuenta'] }}</p>
                                @endif
                                @if (!empty($item['moneda']))
                                    <p class="text-sm mt-1"><span class="text-gray-500">Moneda:</span> {{ $item['moneda'] }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
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
