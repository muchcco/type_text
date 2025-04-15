<div id="playerModal" class="modal">
    <div class="modal-content">
      <span class="close" id="closeModal">&times;</span>
      <h2>Registro de Jugador</h2>
      <form id="playerForm">
        <input type="text" name="alias" id="modalAlias" placeholder="Ingresa tu alias" required>
        <!-- Input hidden para la hora actual -->
        <input type="hidden" name="currentTime" id="currentTime" value="">
        <br>
        <button type="submit">Iniciar Juego</button>
      </form>
    </div>
  </div>
  <script>
    $(document).ready(function(){
      // Al cargar el modal, obtenemos la hora actual y la asignamos al input hidden.
      var now = new Date();
      var hours = now.getHours().toString().padStart(2, '0');
      var minutes = now.getMinutes().toString().padStart(2, '0');
      var seconds = now.getSeconds().toString().padStart(2, '0');
      var currentTime = hours + ':' + minutes + ':' + seconds;
      $("#currentTime").val(currentTime);
  
      // Cerrar el modal al hacer clic en la "X"
      $("#closeModal").on("click", function(){
        $("#playerModal").fadeOut();
      });
    });
  </script>
  