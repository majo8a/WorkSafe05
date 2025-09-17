<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Medida 
    SET id_resultado = ?, 
        tipo_medida = ?, 
        descripcion = ?, 
        id_usuario_responsable = ?, 
        fecha_limite = ?, 
        estado = ? 
    WHERE id_medida = ?");

$stmt->bind_param(
    'ississi', 
    $obj->id_resultado, 
    $obj->tipo_medida, 
    $obj->descripcion, 
    $obj->id_usuario_responsable, 
    $obj->fecha_limite, 
    $obj->estado, 
    $obj->id_medida
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registro modificado"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
