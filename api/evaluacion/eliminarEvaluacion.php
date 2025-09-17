<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Obtener JSON enviado por POST
$obj = json_decode(file_get_contents("php://input"));

// Preparar la consulta de eliminación
$stmt = $db->prepare("DELETE FROM Evaluacion WHERE id_evaluacion = ?");
$stmt->bind_param('i', $obj->id_evaluacion);

// Ejecutar y devolver respuesta JSON
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Evaluación eliminada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

// Cerrar conexión
$stmt->close();
$db->close();
