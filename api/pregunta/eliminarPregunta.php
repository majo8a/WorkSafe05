<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

if (!isset($obj->id_pregunta) || !is_numeric($obj->id_pregunta)) {
  echo json_encode(["status" => "error", "message" => "El ID de la pregunta es obligatorio"]);
  exit;
}

$stmt = $db->prepare("DELETE FROM Pregunta WHERE id_pregunta=?");
$stmt->bind_param("i", $obj->id_pregunta);

if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Pregunta eliminada correctamente"]);
  } else {
    echo json_encode(["status" => "warning", "message" => "No se encontró la pregunta con el ID proporcionado"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Error al eliminar la pregunta: " . $stmt->error]);
}

$stmt->close();
