<?php
require 'conexion.php';
session_start();

if (isset($_POST['recuperar'])) {
  $email = $_POST['email'];

  // Verificar si el correo existe
  $sql = "SELECT * FROM Usuario WHERE correo='$email'";
  $res = mysqli_query($conn, $sql);

  if (mysqli_num_rows($res) > 0) {
    $codigo = rand(100000, 999999);

    // Guardar el código temporalmente en la base
    $sqlUpdate = "UPDATE Usuario SET codigo_recuperacion='$codigo' WHERE correo='$email'";
    mysqli_query($conn, $sqlUpdate);

    // Guardar en sesión
    $_SESSION['email_recuperacion'] = $email;
    $_SESSION['codigo_recuperacion'] = $codigo;

    // Mostrar código directamente (modo local)
    echo "<div style='text-align:center; color:black;'>
            <p>Hola, hemos generado tu código de recuperación:</p>
            <h2 style='color:#011640; font-size:2rem;'>$codigo</h2>
            <p>Ingresa este código para restablecer tu contraseña.</p>
            <a href='verificar_recuperacion.php' class='btn'>Continuar</a>
          </div>";
  } else {
    echo "<div style='text-align:center; color:black;'>
            <p>El correo no está registrado.</p>
            <a href='forgot.php'><button class='btn'>Regresar</button></a>
          </div>";
  }
} else {
?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="../../build/css/app.css">
  </head>

  <body>
    <div class="container-login">
      <div class="form-box box">
        <header>
          <img src='../../src/img/logo-sinfondo.png' alt='Logo'>
          <br>Recuperar Contraseña
        </header>
        <hr>
        <form method="POST" action="">
          <div class="input-container">
            <i class="fa fa-envelope icon-login"></i>
            <input class="input-field" type="email" name="email" placeholder="Correo electrónico" required>
          </div>
          <input type="submit" name="recuperar" value="Generar código" class="btn">
        </form>
        <div style="text-align:center; margin-top:10px;">
          <a href="login.php" style="color:#011640;">Volver al inicio de sesión</a>
        </div>
      </div>
    </div>
  </body>

  </html>
<?php } ?>