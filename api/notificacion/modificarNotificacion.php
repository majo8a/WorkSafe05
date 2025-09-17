<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Notificacion 
    SET tipo = ?, 
        contenido = ?, 
        fecha_envio = ?, 
        estado_general = ?, 
        modulo_origen = ? 
    WHERE id_notificacion = ?");

$stmt->bind_param(
    'sssssi',
    $obj->tipo,
    $obj->contenido,
    $obj->fecha_envio,
    $obj->estado_general,
    $obj->modulo_origen,
    $obj->id_notificacion
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Notificación modificada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
