(function () {
  // =========================
  // CONFIGURACIÓN
  // =========================
  //const TIEMPO_INACTIVO =  10 * 1000; // 10 segundos Prueba
  const TIEMPO_INACTIVO = 10 * 60 * 1000; // 10 minutos
  const URL_LOGOUT = "login/logout.php";

  let temporizador = null;

  // =========================
  // FUNCIONES
  // =========================
  function cerrarSesion() {
    window.location.href = URL_LOGOUT + "?expired=1";
  }

  function reiniciarTemporizador() {
    if (temporizador) {
      clearTimeout(temporizador);
    }
    temporizador = setTimeout(cerrarSesion, TIEMPO_INACTIVO);
  }

  // =========================
  // EVENTOS DE ACTIVIDAD
  // =========================
  window.addEventListener("load", reiniciarTemporizador);
  document.addEventListener("mousemove", reiniciarTemporizador);
  document.addEventListener("keydown", reiniciarTemporizador);
  document.addEventListener("click", reiniciarTemporizador);
  document.addEventListener("scroll", reiniciarTemporizador);
})();
