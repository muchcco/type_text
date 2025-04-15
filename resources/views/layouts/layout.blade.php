<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Juego de Tipeo</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    @yield('main')

    <!-- Contenedor donde se cargará el contenido del modal vía AJAX -->
    <div id="modalContainer"></div>

    @yield('script')
</body>
</html>
