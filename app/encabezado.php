<?php session_start(); ?>
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
  <!-- Encabezado con logo y título -->
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

        <!-- Sección de usuario -->
        <div class="iconos">
          <ul class="nav navbar-nav navbar-right d-flex gap-3 align-items-center">
            <li>
              <a href="#">
                <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Invitado'; ?>
              </a>
            </li>
            <li>
              <a href="../login/logout.php">Salir</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>

  <!-- Barra de pestañas -->
  <div class="barra-pestanas">
    <div class="contenedor">
      <ul class="nav nav-tabs px-3">
        <li class="nav-item">
          <a class="nav-link active" href="home.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="cuestionarios.php">Cuestionarios</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="reportes.php">Reportes</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="configuracion.php">Configuración</a>
        </li>
      </ul>
    </div>
  </div>

</body>

</html>