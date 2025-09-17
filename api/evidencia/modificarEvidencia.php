<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Evidencia 
    SET id_medida = ?, 
        tipo_archivo = ?, 
        ruta_archivo = ?, 
        fecha_carga = ?, 
        id_usuario_subidoPor = ? 
    WHERE id_evidencia = ?");

$stmt->bind_param(
    'isssii', 
    $obj->id_medida, 
    $obj->tipo_archivo, 
    $obj->ruta_archivo, 
    $obj->fecha_carga, 
    $obj->id_usuario_subidoPor,
    $obj->id_evidencia
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registro modificado"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
