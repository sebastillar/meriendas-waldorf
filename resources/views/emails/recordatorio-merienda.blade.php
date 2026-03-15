<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recordatorio merienda</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5;">
    <p>Hola,</p>
    <p>
        Te recordamos que <strong>mañana</strong> a {{ $nombreAlumno }} le toca
        @if ($rol === 'fruta')
            traer la <strong>fruta</strong>.
        @else
            la <strong>elaboración</strong>.
        @endif
    </p>
    <p>Fecha: {{ $fecha }}</p>
    <p>Saludos,<br>Colegio Waldorf</p>
</body>
</html>
