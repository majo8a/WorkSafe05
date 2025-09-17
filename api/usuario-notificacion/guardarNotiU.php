<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("INSERT INTO Usuario_Notificacion (id_usuario, id_notificacion, estado, fecha_visualizacion) 
                      VALUES (?,?,?,?)");

$stmt->bind_param(
    'iiss',
    $obj->id_usuario,
    $obj->id_notificacion,
    $obj->estado,
    $obj->fecha_visualizacion
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registro de usuario_notificación guardado"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
