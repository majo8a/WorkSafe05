<?php
session_start();
require 'conexion.php';
require '../../vendor/autoload.php'; // Asegúrate de tener PHPMailer instalado con Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WorkSafe05</title>
  <link rel="stylesheet" href="../../build/css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="icon" href="../../src/img/logo.png" type="image/x-icon">
</head>

<body>
  <div class="container-login">
    <div class="form-box box">
      <?php

      if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $pass = $_POST['password'];

        // Buscar usuario
        $sql = "SELECT * FROM Usuario WHERE correo='$email'";
        $res = mysqli_query($conn, $sql);

        if (mysqli_num_rows($res) > 0) {
          $row = mysqli_fetch_assoc($res);
          $password_hash = $row['password_hash'];

          if (password_verify($pass, $password_hash)) {
            // Verificar si ya tiene autenticación 2FA activada
            if ($row['autenticacion_dos_factores'] == 1) {

              $_SESSION['id'] = $row['id_usuario'];
              $_SESSION['username'] = $row['nombre_completo'];
              $_SESSION['role'] = $row['id_rol'];

              $redirects = [
                1 => "../menuAdmin.php",
                2 => "../psicologo.php",
                3 => "../home.php"
              ];

              header("location: " . $redirects[$row['id_rol']]);
              exit();
            } else {
              // Generar código de verificación
              $codigo = rand(100000, 999999);

              // Guardar código temporal en sesión
              $_SESSION['codigo_2fa'] = $codigo;
              $_SESSION['email_2fa'] = $email;
              $_SESSION['id_temp'] = $row['id_usuario'];
              $_SESSION['rol_temp'] = $row['id_rol'];
              $_SESSION['nombre_temp'] = $row['nombre_completo'];

              // Enviar correo
              $mail = new PHPMailer(true);
              try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'tu_correo@gmail.com'; // Cambia por tu correo
                $mail->Password = 'tu_contraseña_app'; // Usa contraseña de aplicación
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('tu_correo@gmail.com', 'WorkSafe05');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Código de verificación WorkSafe05';
                $mail->Body = "<p>Hola <b>{$row['nombre_completo']}</b>,</p>
                               <p>Tu código de verificación es:</p>
                               <h2 style='color:#011640;'>$codigo</h2>
                               <p>Ingresa este código para continuar con tu inicio de sesión.</p>";

                // $mail->send();

                // // Redirigir a la verificación
                // header("Location: verificar_codigo.php");
                // exit();
                // MODO LOCAL: Mostrar el código directamente
                echo "<div style='color:black; class='message'><p>Tu código de verificación es: <b style='font-size:1.5rem;'>$codigo</b></p></div>";
                echo "<a href='verificar_codigo.php'><button class='btn'>Continuar</button></a>";
                exit();
              } catch (Exception $e) {
                echo "<div style='color:black; 'class='message'><p>Error al enviar el correo: {$mail->ErrorInfo}</p></div>";
              }
            }
          } else {
            echo "<div style='color:black; class='message'><p style='color:#011640;'>Contraseña incorrecta</p></div><br>";
            echo "<a href='login.php'><button class='btn'>Regresar</button></a>";
          }
        } else {
          echo "<div style='color:black; class='message'><p style='color:#011640;'>Usuario no encontrado</p></div><br>";
          echo "<a href='login.php'><button class='btn'>Regresar</button></a>";
        }
      } else {
      ?>
        <header>
          <img src='../../src/img/logo-sinfondo.png' alt='Logo'></img>
          <br>Iniciar Sesión
        </header>
        <hr>
        <form action="#" method="POST">
          <div class="form-box">
            <div class="input-container">
              <i class="fa fa-envelope icon-login"></i>
              <input class="input-field" type="email" placeholder="Correo electrónico" name="email" required>
            </div>
            <div class="input-container">
              <i class="fa fa-lock icon-login"></i>
              <input class="input-field password" type="password" placeholder="Contraseña" name="password" required>
              <i class="fa fa-eye toggle icon-login"></i>
            </div>
            <div class="remember">
              <input type="checkbox" class="check" name="remember_me">
              <label class="remember-label" for="remember">Recordarme</label>
              <span><a style="font-size: 1.2rem" href="forgot.php">¿Olvidaste tu contraseña?</a></span>
            </div>
          </div>
          <input type="submit" name="login" id="submit" value="Iniciar Sesión" class="btn">
        </form>
      <?php
      }
      ?>
    </div>
  </div>

  <script>
    const toggle = document.querySelector(".toggle"),
      input = document.querySelector(".password");
    toggle.addEventListener("click", () => {
      if (input.type === "password") {
        input.type = "text";
        toggle.classList.replace("fa-eye-slash", "fa-eye");
      } else {
        input.type = "password";
      }
    })
  </script>
</body>

</html>