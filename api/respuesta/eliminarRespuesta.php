<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("DELETE FROM Respuesta WHERE id_respuesta = ?");
$stmt->bind_param('i', $obj->id_respuesta);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Respuesta eliminada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
