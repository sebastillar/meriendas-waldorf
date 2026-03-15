<?php

return [
    /*
    | Canales por los que se envían recordatorios. Por defecto solo email.
    | En el futuro se puede añadir 'whatsapp' cuando exista integración.
    */
    'canales' => ['mail'],

    /*
    | Hora del día (timezone de la app) a la que se ejecuta el job de recordatorios.
    | Se usa en routes/console.php (ej. dailyAt('18:00') para recordatorio del día siguiente).
    */
    'hora' => env('RECORDATORIO_HORA', '18:00'),
];
