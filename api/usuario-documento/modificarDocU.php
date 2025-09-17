<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Usuario_Documento 
    SET id_usuario = ?, 
        id_documento = ?, 
        fecha_asignacion = ?, 
        tipo_acceso = ?, 
        firmado = ?, 
        fecha_firma = ? 
    WHERE id_usuario_doc = ?");

$stmt->bind_param(
    'iissisi',
    $obj->id_usuario,
    $obj->id_documento,
    $obj->fecha_asignacion,
    $obj->tipo_acceso,
    $obj->firmado,
    $obj->fecha_firma,
    $obj->id_usuario_doc
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Asignación modificada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
