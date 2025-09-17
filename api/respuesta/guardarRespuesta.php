<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("INSERT INTO Respuesta (id_pregunta, id_evaluacion, id_opcion_respuesta_select, fecha_respuesta) 
                      VALUES (?, ?, ?, ?)");
$stmt->bind_param(
    'iiis',
    $obj->id_pregunta,
    $obj->id_evaluacion,
    $obj->id_opcion_respuesta_select,
    $obj->fecha_respuesta
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Respuesta registrada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
