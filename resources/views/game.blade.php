@extends('layouts.layout')

@section('main')
<!-- Capas de estrellas -->
<div id="starsLayer1"></div>
<div id="starsLayer2"></div>
<div id="starsLayer3"></div>

<!-- Contenedor principal del juego -->
<div class="game-container" style="position: relative; z-index:10; text-align: center; margin-top: 100px;">
    <h2>Escribe el siguiente texto</h2>
    <div id="textContainer" style="width:80%; margin:20px auto; padding:20px; border:1px solid #fff; background:rgba(0,0,0,0.5); border-radius:5px;">
        <!-- Este párrafo se actualizará en tiempo real con el feedback -->
        <p id="textDisplay">Cargando...</p>
    </div>
    <textarea id="userInput" placeholder="Escribe aquí..." style="width:80%; height:100px; margin:10px 0; border-radius:5px; padding:10px; border:1px solid #fff; background:#222; color:#fff;"></textarea>
    <br>
    <!-- Botón para enviar el puntaje -->
    <button id="submitScoreBtn" style="padding:10px 20px; border:none; border-radius:5px; background:#555; color:#fff; cursor:pointer; margin:5px;" disabled>Enviar Puntaje</button>
</div>
@endsection

@section('script')
<!-- jQuery desde CDN -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
  // Variables globales para la lógica del juego
  let textContent = '';      // Contendrá el texto original obtenido desde el backend
  let requiredCount = 0;     // Valor "count_charc" proveniente de la BD
  let startTime = null;
  let playerId = null;
  let textId = null;

  // Función para generar estrellas de fondo
  function generateStars(containerId, count, minSize, maxSize, duration) {
    const container = $(containerId);
    for (let i = 0; i < count; i++) {
      const size = Math.random() * (maxSize - minSize) + minSize;
      const top = Math.random() * 100;
      const left = Math.random() * 100;
      const star = $('<div class="star"></div>');
      star.css({
        width: size + "px",
        height: size + "px",
        top: top + "%",
        left: left + "%",
        animation: "twinkle " + duration + "s infinite"
      });
      container.append(star);
    }
  }

  // Función para cargar el texto aleatorio y su cantidad requerida
  function loadRandomText() {
    fetch('{{ route("game.randomText") }}')
      .then(response => response.json())
      .then(data => {
        textContent = data.content;
        requiredCount = data.count_charc;  // Valor definido en la BD
        textId = data.id;
        // Actualizamos el display utilizando updateTextDisplay (con input vacío)
        updateTextDisplay();
        console.log("Cantidad requerida de caracteres:", requiredCount);
      })
      .catch(error => console.error('Error al cargar el texto:', error));
  }

  // Función para actualizar en tiempo real el div #textDisplay
  function updateTextDisplay() {
    let typed = $("#userInput").val();
    let displayHtml = "";
    // Recorremos el texto original carácter a carácter
    for (let i = 0; i < textContent.length; i++) {
      let originalChar = textContent.charAt(i);
      if (i < typed.length) {
        let typedChar = typed.charAt(i);
        if (typedChar === originalChar) {
          displayHtml += "<span class='correct'>" + originalChar + "</span>";
        } else {
          displayHtml += "<span class='incorrect'>" + originalChar + "</span>";
        }
      } else {
        displayHtml += "<span class='not-typed'>" + originalChar + "</span>";
      }
    }
    // Si el usuario escribe más caracteres de los que tiene el texto original,
    // se muestran los extra en rojo al final
    if (typed.length > textContent.length) {
      let extra = typed.substring(textContent.length);
      displayHtml += "<span class='incorrect'>" + extra + "</span>";
    }
    $("#textDisplay").html(displayHtml);
  }

  $(document).ready(function(){
    // Generamos las estrellas en cada capa de fondo
    generateStars("#starsLayer1", 100, 1, 2, 2);
    generateStars("#starsLayer2", 70, 1.5, 3, 3);
    generateStars("#starsLayer3", 50, 2, 4, 4);

    // Cargar el modal de registro vía AJAX desde una vista separada.
    $("#modalContainer").load("{{ route('player.modal') }}", function(){
      $("#playerModal").fadeIn();
    });

    // Actualizamos el display cada vez que se escribe en el textarea
    $("#userInput").on("input", updateTextDisplay);
  });

  // Capturamos el envío del formulario del modal para registrar al jugador
  $(document).on('submit', '#playerForm', function(e){
    e.preventDefault();
    const alias = $("#modalAlias").val().trim();    
    if(alias === ""){
      alert('Ingresa un alias.');
      return;
    }
    // Enviar el alias para registrar al jugador mediante AJAX
    $.ajax({
      type: 'POST',
      url: '{{ route("game.registerPlayer") }}',
      data: JSON.stringify({ alias: alias }),
      contentType: 'application/json',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      success: function(data){
        playerId = data.id;
        $("#playerModal").fadeOut();  // Oculta el modal de registro
        loadRandomText();
        startTime = new Date();
        $("#submitScoreBtn").prop("disabled", false);
      },
      error: function(error){
        console.error("Error al registrar el jugador:", error);
      }
    });
  });

  //Para la siguiente ronda

  function nextGame() {
    $.ajax({
      type: 'POST',
      url: '{{ route("next.modal") }}',
      // data: JSON.stringify({ alias: alias }),
      contentType: 'application/json',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      success: function(data){
        playerId = data.id;
        $("#playerModal").load(data.html);
        // $("#playerModal").fadeOut();  // Oculta el modal de registro
        loadRandomText();
        startTime = new Date();
        $("#submitScoreBtn").prop("disabled", false);
      },
      error: function(error){
        console.error("Error al registrar el jugador:", error);
      }
    });
  }

  // Al hacer click en "Enviar Puntaje" se calcula y envía el puntaje
  $("#submitScoreBtn").on("click", function(){
    let endTime = new Date();
    let timeTaken = (endTime - startTime) / 1000; // en segundos
    let typedCount = $("#userInput").val().length;
    let score = Math.round((typedCount / requiredCount) * 100);
    const time_first = $("#currentTime").val().trim();

    fetch('{{ route("game.storeScore") }}', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ 
          player_id: playerId, 
          text_id: textId, 
          score: score, 
          time_taken: timeTaken,
          character_count: typedCount,
          time_first: time_first
      })
    })
    .then(response => {
       if (!response.ok) {
         return response.text().then(text => { throw new Error(text); });
       }
       return response.json();
    })
    .then(data => {
      //  alert('¡Puntaje registrado con éxito!\nPuntaje: ' + score);
       $("#userInput").val('');
       nextGame();
       updateTextDisplay(); // Reinicia el feedback (muestra el texto sin input)
       loadRandomText();
       startTime = new Date();
    })
    .catch(error => console.error('Error al registrar el puntaje:', error));
  });
</script>
@endsection
