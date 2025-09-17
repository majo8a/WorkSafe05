<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Bitacora 
    SET id_usuario = ?, 
        accion = ?, 
        modulo = ?, 
        descripcion = ?, 
        fecha_evento = ?, 
        objeto = ?, 
        id_objeto = ?, 
        ip_origen = ? 
    WHERE id_bitacora = ?");

$stmt->bind_param(
    'issssisis',
    $obj->id_usuario,
    $obj->accion,
    $obj->modulo,
    $obj->descripcion,
    $obj->fecha_evento,
    $obj->objeto,
    $obj->id_objeto,
    $obj->ip_origen,
    $obj->id_bitacora
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Bitácora modificada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
