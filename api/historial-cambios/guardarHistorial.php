<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("INSERT INTO Historial_Cambios 
    (id_usuario_responsable, tipo_objeto, id_objeto, campo, valor_antiguo, valor_nuevo, fecha_cambio) 
    VALUES (?,?,?,?,?,?,?)");

$stmt->bind_param(
    'sisssss',
    $obj->id_usuario_responsable,
    $obj->tipo_objeto,
    $obj->id_objeto,
    $obj->campo,
    $obj->valor_antiguo,
    $obj->valor_nuevo,
    $obj->fecha_cambio
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Cambio registrado en historial"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
