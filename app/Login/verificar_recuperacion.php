<?php
require 'conexion.php';
session_start();

if (isset($_POST['verificar'])) {
  $codigo = $_POST['codigo'];
  $email = $_SESSION['email_recuperacion'] ?? '';

  $sql = "SELECT * FROM Usuario WHERE correo='$email' AND codigo_recuperacion='$codigo'";
  $res = mysqli_query($conn, $sql);

  if (mysqli_num_rows($res) > 0) {
    $_SESSION['recuperacion_verificada'] = true;
    header("Location: reset_password.php");
    exit();
  } else {
    echo "<div style='text-align:center; color:black;'>
            <p>Código incorrecto, intenta de nuevo.</p>
            <a href='verificar_recuperacion.php'><button class='btn'>Reintentar</button></a>
          </div>";
  }
} else {
?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <meta charset="UTF-8">
    <title>Verificar Código</title>
    <link rel="stylesheet" href="../../build/css/app.css">
  </head>

  <body>
    <div class="container-login">
      <div class="form-box box">
        <header>
          <img src='../../src/img/logo-sinfondo.png' alt='Logo'>
          <br>Verificar Código
        </header>
        <hr>
        <form method="POST" action="">
          <div class="input-container">
            <i class="fa fa-key icon-login"></i>
            <input class="input-field" type="text" name="codigo" placeholder="Código de 6 dígitos" required>
          </div>
          <input type="submit" name="verificar" value="Verificar" class="btn">
        </form>
      </div>
    </div>
  </body>

  </html>
<?php } ?>