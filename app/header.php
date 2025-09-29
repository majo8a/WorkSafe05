<?php
session_start();

$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : "Invitado";
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : "invitado";
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Menú dinámico</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>

  <!-- Encabezado -->
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="home.php">Mi Sistema</a>
      </div>

      <!-- Aquí irá el menú dinámico -->
      <ul id="menu-principal" class="nav navbar-nav"></ul>

      <!-- Usuario y logout/login -->
      <ul id="menu-derecha" class="nav navbar-nav navbar-right"></ul>
    </div>
  </nav>

  <script>
    // Variables PHP -> JS
    const usuario = "<?php echo $usuario; ?>";
    const rol = "<?php echo $rol; ?>";
  </script>

  <script src="menuController.js"></script>
</body>

</html>