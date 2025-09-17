<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Capacitacion 
    SET tema = ?, 
        descripcion = ?, 
        fecha_inicio = ?, 
        fecha_fin = ?, 
        tipo_modalidad = ?, 
        id_usuario_asignador = ? 
    WHERE id_capacitacion = ?");

$stmt->bind_param(
    'ssssiii', 
    $obj->tema, 
    $obj->descripcion, 
    $obj->fecha_inicio, 
    $obj->fecha_fin, 
    $obj->tipo_modalidad, 
    $obj->id_usuario_asignador,
    $obj->id_capacitacion
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Capacitación modificada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
