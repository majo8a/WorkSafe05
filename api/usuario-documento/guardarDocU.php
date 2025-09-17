<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("INSERT INTO Usuario_Documento (id_usuario, id_documento, fecha_asignacion, tipo_acceso, firmado, fecha_firma) 
                      VALUES (?,?,?,?,?,?)");

$stmt->bind_param(
    'iissis',
    $obj->id_usuario,
    $obj->id_documento,
    $obj->fecha_asignacion,
    $obj->tipo_acceso,
    $obj->firmado,
    $obj->fecha_firma
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Asignación registrada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
