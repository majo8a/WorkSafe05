<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

if (!isset($obj->id_pregunta) || !is_numeric($obj->id_pregunta)) {
  echo json_encode(["status" => "error", "message" => "El ID de la pregunta es obligatorio"]);
  exit;
}

$stmt = $db->prepare("
    SELECT p.*, c.nombre as nombre_cuestionario
    FROM Pregunta p
    LEFT JOIN Cuestionario c ON p.id_cuestionario = c.id_cuestionario
    WHERE p.id_pregunta=?
");
$stmt->bind_param("i", $obj->id_pregunta);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode($row);
} else {
  echo json_encode(["status" => "error", "message" => "Pregunta no encontrada"]);
}

$stmt->close();
