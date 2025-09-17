<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Confirmacion 
    SET id_usuario = ?, 
        id_capacitacion = ?, 
        tipo_confirmacion = ?, 
        fecha_confirmacion = ?, 
        ip_registro = ?, 
        asistio = ? 
    WHERE id_confirmacion = ?");

$stmt->bind_param(
    'iisssii',
    $obj->id_usuario,
    $obj->id_capacitacion,
    $obj->tipo_confirmacion,
    $obj->fecha_confirmacion,
    $obj->ip_registro,
    $obj->asistio,
    $obj->id_confirmacion
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Confirmación modificada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
