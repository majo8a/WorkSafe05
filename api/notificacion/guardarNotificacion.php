<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("INSERT INTO Notificacion (tipo, contenido, fecha_envio, estado_general, modulo_origen) 
                      VALUES (?,?,?,?,?)");

$stmt->bind_param(
    'sssss',
    $obj->tipo,
    $obj->contenido,
    $obj->fecha_envio,
    $obj->estado_general,
    $obj->modulo_origen
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Notificación registrada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
