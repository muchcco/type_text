<div id="playerModal" class="modal">
    <div class="modal-content">
      <span class="close" id="closeModal">&times;</span>
      <h2>Registro de Jugador</h2>
      <form id="playerForm">
        <input type="text" name="alias" id="modalAlias" placeholder="Ingresa tu alias" required>
        <br>
        <button type="submit">Iniciar Juego</button>
      </form>
    </div>
  </div>
  <script>
    // Cerrar el modal al hacer clic en la "X"
    $("#closeModal").on("click", function(){
      $("#playerModal").fadeOut();
    });
  </script>
  