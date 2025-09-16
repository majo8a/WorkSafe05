<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkSafe035</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Dosis:wght@200..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../build/css/app.css">
    <link rel="icon" href="../src/img/logo.png" type="image/x-icon">

<!-- Encabezado con logo y título -->
<div class="navegador">
  <nav class="navbar px-3 border-bottom w-100">
    <div class="contenedor d-flex justify-content-between align-items-center">
      <a class="navbar-brand d-flex align-items-center" href="home.php">
        <img class="nav-logo" src="..src/img/logo-sinfondo.png" alt="Logo">
        <div>
          <div class="system-title">EPSINOM-035</div>
          <div class="system-subtitle">Evaluación psicosocial con base en la NOM-035</div>
        </div>
      </a>
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

<!-- Barra de pestañas -->
<div class="barra-pestanas">
  <div class="contenedor">
    <ul class="nav nav-tabs px-3">
      <li class="nav-item">
        <a class="nav-link active" href="home.php">Inicio</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Cuestionarios</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Reportes</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Configuración</a>
      </li>
    </ul>
  </div>
</div>
</head>