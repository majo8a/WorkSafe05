<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Usuario_Notificacion 
    SET id_usuario = ?, 
        id_notificacion = ?, 
        estado = ?, 
        fecha_visualizacion = ? 
    WHERE id_usuario_notificacion = ?");

$stmt->bind_param(
    'iissi',
    $obj->id_usuario,
    $obj->id_notificacion,
    $obj->estado,
    $obj->fecha_visualizacion,
    $obj->id_usuario_notificacion
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Usuario_Notificación modificada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
