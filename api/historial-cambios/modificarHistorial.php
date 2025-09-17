<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Historial_Cambios 
    SET id_usuario_responsable = ?, 
        tipo_objeto = ?, 
        id_objeto = ?, 
        campo = ?, 
        valor_antiguo = ?, 
        valor_nuevo = ?, 
        fecha_cambio = ? 
    WHERE id_cambio = ?");

$stmt->bind_param(
    'sisssssi',
    $obj->id_usuario_responsable,
    $obj->tipo_objeto,
    $obj->id_objeto,
    $obj->campo,
    $obj->valor_antiguo,
    $obj->valor_nuevo,
    $obj->fecha_cambio,
    $obj->id_cambio
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Historial de cambio modificado"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
