<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WorkSafe05</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link rel="stylesheet" href="../build/css/app.css">
    <link rel="icon" href="../src/img/logo.png" type="image/x-icon">
</head>

<body>
  <header class="header">
    <div class="navegador">
      <nav class="navbar px-3 border-bottom w-100">
        <div class="contenedor d-flex justify-content-between align-items-center">
          <a class="navbar-brand d-flex align-items-center" href="home.php">
            <img class="nav-logo" src="../src/img/logo-sinfondo.png" alt="Logo">
            <div>
              <div class="system-title">EPSINOM-035</div>
              <div class="system-subtitle">Evaluación psicosocial con base en la NOM-035</div>
            </div>
          </a>
        </div>
        <div class="iconos">
          <div class="usuario dropdown">
            <div class="user-link dropdown-toggle">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <circle cx="12" cy="7" r="4" />
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
              </svg>
            </div>
            <div class="dropdown-content">
              <a style="font-size: 18px" href="logout.php">Cerrar sesión</a>
            </div>
          </div>
        </div>
    </div>
    </nav>
    </div>
    <div class="barra-pestanas">
      <div class="contenedor">
        <ul class="nav nav-tabs px-3">
          <li class="nav-item"><a class="nav-link active" href="home.php">Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="cuestionarios.php">Cuestionarios</a></li>
          <li class="nav-item"><a class="nav-link" href="reportes.php">Reportes</a></li>
          <li class="nav-item"><a class="nav-link" href="configuracion.php">Configuración</a></li>
        </ul>
      </div>
    </div>
  </header>

  <header class="menu-hamburguesa">
    <button type="button" class="btn-hamburguesa" id="btnHamburguesa" aria-controls="menuHamburguesa" aria-expanded="false" aria-label="Abrir menú">
      &#9776;
    </button>

    <div class="navegador">
      <nav class="navbar px-3 border-bottom w-100">
        <div class="contenedor d-flex justify-content-between align-items-center">
          <a class="navbar-brand d-flex align-items-center" href="home.php">
            <img class="nav-logo" src="../src/img/logo-sinfondo.png" alt="Logo">
            <div>
              <div class="system-title">EPSINOM-035</div>
              <div class="system-subtitle">Evaluación psicosocial con base en la NOM-035</div>
            </div>
          </a>
        </div>
      </nav>

      <!-- menú colapsable -->
      <div class="barra-pestanas" id="menuHamburguesa" aria-hidden="true">
        <div class="contenedor">
          <ul class="nav nav-tabs px-3">
            <li class="nav-item"><a class="nav-link" href="login.php">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <circle cx="12" cy="7" r="4" />
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
              </svg>
            </a></li>
            <li class="nav-item"><a class="nav-link" href="home.php">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="cuestionarios.php">Cuestionarios</a></li>
            <li class="nav-item"><a class="nav-link" href="reportes.php">Reportes</a></li>
            <li class="nav-item"><a class="nav-link" href="configuracion.php">Configuración</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("btnHamburguesa");
    const menu = document.getElementById("menuHamburguesa");

    if (!btn) {
      console.error("btnHamburguesa no encontrado");
      return;
    }
    if (!menu) {
      console.error("menuHamburguesa no encontrado");
      return;
    }

    // toggle menú
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const activo = menu.classList.toggle("activo");
      btn.setAttribute("aria-expanded", activo ? "true" : "false");
      menu.setAttribute("aria-hidden", activo ? "false" : "true");
    });

    // cerrar al dar clic fuera
    document.addEventListener("click", function (e) {
      if (!e.target.closest("#menuHamburguesa") && !e.target.closest("#btnHamburguesa")) {
        if (menu.classList.contains("activo")) {
          menu.classList.remove("activo");
          btn.setAttribute("aria-expanded", "false");
          menu.setAttribute("aria-hidden", "true");
        }
      }
    });

    // cerrar con ESC
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && menu.classList.contains("activo")) {
        menu.classList.remove("activo");
        btn.setAttribute("aria-expanded", "false");
        menu.setAttribute("aria-hidden", "true");
      }
    });
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>