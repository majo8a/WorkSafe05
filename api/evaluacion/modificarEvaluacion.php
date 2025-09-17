<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Obtener JSON enviado por POST
$obj = json_decode(file_get_contents("php://input"));

// Preparar la consulta de actualización
$stmt = $db->prepare("UPDATE Evaluacion 
    SET id_usuario = ?, 
        id_cuestionario = ?, 
        fecha_aplicacion = ?, 
        estado = ?, 
        id_usuario_aplicador = ? 
    WHERE id_evaluacion = ?");

// Vincular parámetros
$stmt->bind_param(
    'iissii',
    $obj->id_usuario,
    $obj->id_cuestionario,
    $obj->fecha_aplicacion,
    $obj->estado,
    $obj->id_usuario_aplicador,
    $obj->id_evaluacion
);

// Ejecutar y devolver respuesta JSON
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Evaluación modificada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

// Cerrar conexión
$stmt->close();
$db->close();
