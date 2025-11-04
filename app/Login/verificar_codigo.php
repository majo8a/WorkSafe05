<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['codigo_2fa'])) {
  header("Location: login.php");
  exit();
}

if (isset($_POST['verificar'])) {
  $codigo_ingresado = $_POST['codigo'];
  if ($codigo_ingresado == $_SESSION['codigo_2fa']) {

    // Actualizar autenticación en la base de datos
    $id = $_SESSION['id_temp'];
    mysqli_query($conn, "UPDATE Usuario SET autenticacion_dos_factores = 1 WHERE id_usuario = $id");

    // Crear sesión definitiva
    $_SESSION['id'] = $id;
    $_SESSION['username'] = $_SESSION['nombre_temp'];
    $_SESSION['role'] = $_SESSION['rol_temp'];

    // Limpiar temporales
    unset($_SESSION['codigo_2fa'], $_SESSION['email_2fa'], $_SESSION['id_temp'], $_SESSION['rol_temp'], $_SESSION['nombre_temp']);

    // Redirigir según rol
    $redirects = [
      1 => "../menuAdmin.php",
      2 => "../psicologo.php",
      3 => "../home.php"
    ];

    header("Location: " . $redirects[$_SESSION['role']]);
    exit();
  } else {
    $error = "Código incorrecto. Intenta nuevamente.";
  }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Verificación 2FA</title>
  <link rel="stylesheet" href="../../build/css/app.css">
</head>

<body>
  <div class="container-login">
    <div class="form-box box">
      <header>Verificación en dos pasos</header>
      <hr>
      <p>Se envió un código de 6 dígitos a tu correo. Escríbelo a continuación:</p>
      <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
      <form method="POST">
        <input type="text" name="codigo" maxlength="6" placeholder="Ingresa el código" required class="input-field">
        <input type="submit" name="verificar" value="Verificar" class="btn">
      </form>
    </div>
  </div>
</body>

</html>