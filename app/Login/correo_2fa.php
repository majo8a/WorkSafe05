<?php
// correo_2fa.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '../../vendor/autoload.php';

function enviarCodigo2FA($destinoEmail, $nombreUsuario, $codigo)
{
  $mail = new PHPMailer(true);
  try {
    // Configuración SMTP - adapta estos datos con los de tu proveedor
    $mail->isSMTP();
    $mail->Host = 'smtp.tudominio.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'no-reply@tudominio.com';
    $mail->Password = 'TU_PASSWORD_SMTP';
    $mail->SMTPSecure = 'tls'; // o 'ssl'
    $mail->Port = 587; // puerto

    $mail->setFrom('no-reply@tudominio.com', 'WorkSafe05');
    $mail->addAddress($destinoEmail, $nombreUsuario);

    $mail->isHTML(true);
    $mail->Subject = 'Código de verificación - WorkSafe05';
    $mailBody = "
            <p>Hola <strong>{$nombreUsuario}</strong>,</p>
            <p>Tu código de verificación es: <strong>{$codigo}</strong></p>
            <p>Este código expirará en 10 minutos.</p>
            <p>Si no solicitaste este código, ignora este correo.</p>
        ";
    $mail->Body = $mailBody;

    $mail->send();
    return true;
  } catch (Exception $e) {
    error_log("Error enviando correo 2FA: " . $mail->ErrorInfo);
    return false;
  }
}
