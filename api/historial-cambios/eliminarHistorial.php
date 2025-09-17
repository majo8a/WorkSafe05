<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("DELETE FROM Historial_Cambios WHERE id_cambio = ?");
$stmt->bind_param('i', $obj->id_cambio);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Historial de cambio eliminado"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
