<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("INSERT INTO Bitacora (id_usuario, accion, modulo, descripcion, fecha_evento, objeto, id_objeto, ip_origen) 
                      VALUES (?,?,?,?,?,?,?,?)");

$stmt->bind_param(
    'isssssis',
    $obj->id_usuario,
    $obj->accion,
    $obj->modulo,
    $obj->descripcion,
    $obj->fecha_evento,
    $obj->objeto,
    $obj->id_objeto,
    $obj->ip_origen
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registro de bitácora guardado"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
