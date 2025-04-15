<div id="playerModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeModal">&times;</span>
    <h2>Registro de Jugador</h2>
    <form id="playerForm">
      <input type="text" name="alias" id="modalAlias" placeholder="Ingresa tu alias" required>
      <!-- Input hidden con la hora actual del servidor utilizando Carbon -->
      <input type="hidden" name="currentTime" id="currentTime" value="{{ \Carbon\Carbon::now()->format('H:i:s') }}">
      <br>
      <button type="submit">Iniciar Juego</button>
    </form>
  </div>
</div>
<script>
  $(document).ready(function(){
    $("#closeModal").on("click", function(){
      $("#playerModal").fadeOut();
    });
  });
</script>
