<?php

return [
    'regalo' => [
        'monto_aportar' => (float) env('MERIENDAS_MONTO_APORTAR_REGALO', 300),
    ],
    'notificaciones' => [
        'dias_antelacion' => (int) env('MERIENDAS_NOTIFICAR_DIAS_ANTES', 1),
        'hora_envio' => env('MERIENDAS_NOTIFICAR_HORA', '08:00'),
    ],
    'asignacion' => [
        'mismo_alumno_fruta_elaboracion' => (bool) env('MERIENDAS_MISMO_ALUMNO_FRUTA_ELAB', false),
        /** Día del mes en que se recalculan/generan las asignaciones del mes siguiente (1-28). Configurable con MERIENDAS_DIA_RECALCULO_ASIGNACIONES. */
        'dia_recalculo_mes_siguiente' => (int) env('MERIENDAS_DIA_RECALCULO_ASIGNACIONES', 25),
    ],
];
