<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['pending_2fa_user'])) {
  header("Location: login.php");
  exit();
}

$id_usuario = $_SESSION['pending_2fa_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $codigo_ingresado = trim($_POST['codigo']);

  // Buscar el último código no usado para este usuario
  $stmt = $conn->prepare("SELECT id_code, code_hash, expires_at, used, attempts FROM TwoFactorCodes WHERE id_usuario = ? ORDER BY created_at DESC LIMIT 1");
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();

    if ((int)$row['used'] === 1) {
      $error = "Este código ya fue usado. Solicita uno nuevo.";
    } else {
      $expires_at = new DateTime($row['expires_at']);
      $now = new DateTime();

      if ($now > $expires_at) {
        $error = "El código expiró. Solicita uno nuevo desde el inicio de sesión.";
      } else {
        // Limitar intentos
        if ($row['attempts'] >= 5) {
          $error = "Has excedido el número máximo de intentos. Solicita un nuevo código.";
        } else {
          // Verificar hash
          if (password_verify($codigo_ingresado, $row['code_hash'])) {
            // Marcar código como usado y actualizar Usuario.autenticacion_dos_factores = 1
            $upd = $conn->prepare("UPDATE TwoFactorCodes SET used = 1 WHERE id_code = ?");
            $upd->bind_param("i", $row['id_code']);
            $upd->execute();

            $updU = $conn->prepare("UPDATE Usuario SET autenticacion_dos_factores = 1 WHERE id_usuario = ?");
            $updU->bind_param("i", $id_usuario);
            $updU->execute();

            // Completar login: cargar datos del usuario
            $u = $conn->prepare("SELECT id_usuario, nombre_completo, id_rol FROM Usuario WHERE id_usuario = ?");
            $u->bind_param("i", $id_usuario);
            $u->execute();
            $resu = $u->get_result();
            $user = $resu->fetch_assoc();

            $_SESSION['id'] = $user['id_usuario'];
            $_SESSION['username'] = $user['nombre_completo'];
            $_SESSION['role'] = $user['id_rol'];
            unset($_SESSION['pending_2fa_user']);

            // Redireccionar según rol
            $redirects = [
              1 => "../menuAdmin.php",
              2 => "../psicologo.php",
              3 => "../home.php"
            ];
            if (array_key_exists($user['id_rol'], $redirects)) {
              header("Location: " . $redirects[$user['id_rol']]);
              exit();
            } else {
              echo "Rol desconocido";
            }
          } else {
            // incrementar intentos
            $inc = $conn->prepare("UPDATE TwoFactorCodes SET attempts = attempts + 1 WHERE id_code = ?");
            $inc->bind_param("i", $row['id_code']);
            $inc->execute();
            $error = "Código incorrecto.";
          }
        }
      }
    }
  } else {
    $error = "No se encontró un código válido. Regresa e intenta iniciar sesión de nuevo.";
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <title>Verificación 2FA</title>
</head>

<body>
  <h2>Verificación de código</h2>
  <p>Te enviamos un código al correo asociado a tu cuenta. Ingresa el código de 6 dígitos.</p>

  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

  <form method="post">
    <input type="text" name="codigo" maxlength="6" pattern="\d{6}" required placeholder="123456">
    <button type="submit">Verificar</button>
  </form>

  <p>
    ¿No recibiste el código? <a href="resend_2fa.php">Reenviar código</a> <!-- crear reenvío seguro -->
  </p>
</body>

</html>