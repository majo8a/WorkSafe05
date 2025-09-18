<?php
session_start();
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
      include "connection.php";

      if (isset($_POST['login'])) {

        $email = $_POST['email'];
        $pass = $_POST['password'];

        $sql = "SELECT * FROM users WHERE email='$email'";
        $res = mysqli_query($conn, $sql);

        if (mysqli_num_rows($res) > 0) {
          $row = mysqli_fetch_assoc($res);
          $password = $row['password'];

          if (password_verify($pass, $password)) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Redirigir según el rol del usuario 1=user 2= admin
            if ($row['role'] == 1) {
              header("location: ../home.php");
            } elseif ($row['role'] == 2) {
              header("location: ../configuracion.php");
            } else {
              echo "<div class='message'><p>Rol desconocido</p></div><br>";
              echo "<a href='login.php'><button class='btn'>Regresar</button></a>";
            }
          } else {
            echo "<div class='message'><p>Contraseña incorrecta</p></div><br>";
            echo "<a href='login.php'><button class='btn'>Regresar</button></a>";
          }
        } else {
          echo "<div class='message'><p>Correo electrónico o contraseña incorrectos</p></div><br>";
          echo "<a href='login.php'><button class='btn'>Regresar</button></a>";
        }
      } else {
      ?>
      <header>
      <img src="../../src/img/logo-sinfondo.png" alt="Logo"></img>
      <br>
      Iniciar Sesión</header>
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
    </div>
    <?php
      }
    ?>
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
