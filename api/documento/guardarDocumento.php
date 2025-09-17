<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("INSERT INTO Documento (titulo, descripcion, ruta_archivo, fecha_publicacion, id_usuario_publicador, acceso_roles) 
                      VALUES (?,?,?,?,?,?)");

$stmt->bind_param(
    'ssssis',
    $obj->titulo,
    $obj->descripcion,
    $obj->ruta_archivo,
    $obj->fecha_publicacion,
    $obj->id_usuario_publicador,
    $obj->acceso_roles
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Documento registrado"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
