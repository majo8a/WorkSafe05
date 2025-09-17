<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("DELETE FROM Usuario_Notificacion WHERE id_usuario_notificacion = ?");
$stmt->bind_param('i', $obj->id_usuario_notificacion);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Usuario_Notificación eliminada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
