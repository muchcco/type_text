@extends('layouts.layout')

@section('main')
  <!-- MODAL DE REGISTRO (se cargará vía AJAX) -->
  <div id="modalContainer"></div>

  <!-- MODAL DE CONFIRMACIÓN PARA SIGUIENTE RONDA -->
  <div id="continueModal" class="modal" style="display:none;">
    <div class="modal-content">
      <h2>¿Deseas continuar jugando?</h2>
      <button id="continueBtn">Continuar</button>
      <button id="withdrawBtn">Retirarse</button>
    </div>
  </div>

  <!-- Capas de estrellas (layout.js se encargará de generarlas) -->
  <div id="starsLayer1"></div>
  <div id="starsLayer2"></div>
  <div id="starsLayer3"></div>

  <!-- Contenedor principal del juego -->
  <div class="game-container" style="position: relative; z-index:10; text-align: center; margin-top: 100px;">
    <h2>Escribe el siguiente texto</h2>
    <div id="textContainer"
         style="width:80%; margin:20px auto; padding:20px; border:1px solid #fff;
                background:rgba(0,0,0,0.5); border-radius:5px;">
      <p id="textDisplay">Cargando...</p>
    </div>
    <textarea id="userInput"
              placeholder="Escribe aquí..."
              style="width:80%; height:100px; margin:10px 0; border-radius:5px;
                     padding:10px; border:1px solid #fff; background:#222; color:#fff;"></textarea>
    <br>
    <button id="submitScoreBtn"
            style="padding:10px 20px; border:none; border-radius:5px;
                   background:#555; color:#fff; cursor:pointer; margin:5px;"
            disabled>
      Enviar Puntaje
    </button>
  </div>
@endsection

@section('script')
<script>
  // Variables de juego
  let textContent = '', requiredCount = 0, startTime, playerId, textId;

  // Carga un texto aleatorio
  function loadRandomText() {
    fetch('{{ route("game.randomText") }}')
      .then(r => r.json())
      .then(data => {
        textContent   = data.content;
        requiredCount = data.count_charc;
        textId        = data.id;
        updateTextDisplay();
        startTime     = new Date();
      })
      .catch(e => console.error('Error al cargar texto:', e));
  }

  // Feedback en tiempo real
  function updateTextDisplay() {
    let typed = $("#userInput").val(), html = '';
    for (let i = 0; i < textContent.length; i++) {
      let c = textContent.charAt(i);
      html += (i < typed.length)
             ? (typed.charAt(i) === c
                ? `<span class="correct">${c}</span>`
                : `<span class="incorrect">${c}</span>`)
             : `<span class="not-typed">${c}</span>`;
    }
    if (typed.length > textContent.length) {
      html += `<span class="incorrect">${typed.slice(textContent.length)}</span>`;
    }
    $("#textDisplay").html(html);
  }

  $(function(){
    // 1) Cargar modal de registro
    $("#modalContainer").load("{{ route('player.modal') }}", () => {
      $("#playerModal").fadeIn();
    });

    // 2) Feedback
    $("#userInput").on("input", updateTextDisplay);

    // 3) Registrar jugador
    $(document).on('submit', '#playerForm', function(e){
      e.preventDefault();
      let alias = $("#modalAlias").val().trim();
      if (!alias) return alert('Ingresa un alias.');
      $.ajax({
        type: 'POST',
        url: '{{ route("game.registerPlayer") }}',
        contentType: 'application/json',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        data: JSON.stringify({ alias }),
        success(data) {
          playerId = data.id;
          $("#playerModal").fadeOut();
          loadRandomText();
          $("#submitScoreBtn").prop("disabled", false);
        },
        error(err) {
          console.error('Error al registrar jugador:', err);
        }
      });
    });

    // 4) Enviar puntaje
    $("#submitScoreBtn").on("click", function(){
      let endTime    = new Date(),
          timeTaken  = (endTime - startTime)/1000,
          typedCount = $("#userInput").val().length,
          score      = Math.round(typedCount/requiredCount * 100);
      fetch('{{ route("game.storeScore") }}', {
        method:'POST',
        headers:{
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({
          player_id: playerId,
          text_id:   textId,
          score,
          time_taken: timeTaken,
          character_count: typedCount
        })
      })
      .then(r => {
        if (!r.ok) return r.text().then(t => { throw new Error(t); });
        return r.json();
      })
      .then(() => $("#continueModal").fadeIn())
      .catch(err => console.error('Error al registrar puntaje:', err));
    });

    // 5) Continuar o retirarse
    $("#continueBtn").click(function(){
      $("#continueModal").fadeOut();
      $("#userInput").val('');
      loadRandomText();
    });
    $("#withdrawBtn").click(function(){
      window.location = '{{ route("game.report", ["player" => "__ID__"]) }}'
                         .replace('__ID__', playerId);
    });
  });
</script>
@endsection
