<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Asignación por intercambio</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5;">
    <p>Hola,</p>
    <p>
        Se realizó un intercambio y a <strong>{{ $nombreAlumno }}</strong> le quedó asignado
        @if ($rol === 'fruta')
            traer la <strong>fruta</strong>
        @else
            la <strong>elaboración</strong>
        @endif
        para el día <strong>{{ $fecha }}</strong>.
    </p>
    <p>Saludos,<br>Meriendas Waldorf</p>
</body>
</html>
