<?php
require 'conexion.php';
session_start();

if (!isset($_SESSION['recuperacion_verificada']) || !isset($_SESSION['email_recuperacion'])) {
  header("Location: forgot.php");
  exit();
}

if (isset($_POST['reset'])) {
  $newPass = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $email = $_SESSION['email_recuperacion'];

  $sql = "UPDATE Usuario SET password_hash='$newPass', codigo_recuperacion=NULL WHERE correo='$email'";
  mysqli_query($conn, $sql);

  // Limpiar variables de sesión
  unset($_SESSION['recuperacion_verificada']);
  unset($_SESSION['email_recuperacion']);

  // Mostrar mensaje con mismo diseño
  echo "
  <!DOCTYPE html>
  <html lang='es'>
  <head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Contraseña Restablecida</title>
    <link rel='stylesheet' href='../../build/css/app.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <link rel='icon' href='../../src/img/logo.png' type='image/x-icon'>
  </head>
  <body>
    <div class='container-login'>
      <div class='form-box box'>
        <header>
          <img src='../../src/img/logo-sinfondo.png' alt='Logo'>
          <br>Contraseña Restablecida
        </header>
        <hr>
        <div style='text-align:center; padding:20px; color:black;'>
          <p style='font-size:1.5rem;'>Tu contraseña ha sido actualizada correctamente.</p>
          <p>Puedes iniciar sesión con tu nueva contraseña.</p>
        </div>
        <div style='text-align:center; margin-top:20px;'>
          <a href='login.php' class='btn'>Iniciar Sesión</a>
        </div>
      </div>
    </div>
  </body>
  </html>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva Contraseña</title>
  <link rel="stylesheet" href="../../build/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="icon" href="../../src/img/logo.png" type="image/x-icon">
</head>

<body>
  <div class="container-login">
    <div class="form-box box">
      <header>
        <img src='../../src/img/logo-sinfondo.png' alt='Logo'>
        <br>Nueva Contraseña
      </header>
      <hr>
      <form method="POST" action="">
        <div class="input-container">
          <i class="fa fa-lock icon-login"></i>
          <input class="input-field" type="password" name="password" placeholder="Nueva contraseña" required>
        </div>
        <input type="submit" name="reset" value="Restablecer" class="btn">
      </form>
    </div>
  </div>
</body>

</html>