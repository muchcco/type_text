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

    <!-- jQuery (necesario para las estrellas y modales) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
      /**
       * generateStars: crea n estrellas aleatorias dentro de un contenedor
       * @param {string} selector  – id o clase del contenedor
       * @param {number} count     – cuántas estrellas
       * @param {number} minSize   – tamaño mínimo en px
       * @param {number} maxSize   – tamaño máximo en px
       * @param {number} duration  – duración de la animación en segundos
       */
      function generateStars(selector, count, minSize, maxSize, duration) {
        const $c = $(selector);
        if (!$c.length) return; // no existe ese contenedor en esta vista
        for (let i = 0; i < count; i++) {
          const size  = Math.random() * (maxSize - minSize) + minSize;
          const top   = Math.random() * 100;
          const left  = Math.random() * 100;
          const star  = $('<div class="star"></div>').css({
            width:  size + "px",
            height: size + "px",
            top:    top + "%",
            left:   left + "%",
            animation: "twinkle " + duration + "s infinite"
          });
          $c.append(star);
        }
      }

      // Autoejecución al cargar cualquier página que tenga #starsLayer1/2/3
      $(function(){
        generateStars("#starsLayer1", 100, 1,   2,  2);
        generateStars("#starsLayer2", 70,  1.5, 3,  3);
        generateStars("#starsLayer3", 50,  2,   4,  4);
      });
    </script>

    @yield('script')
</body>
</html>
