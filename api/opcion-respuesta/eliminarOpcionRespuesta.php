<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

if (!isset($obj->id_opcion) || !is_numeric($obj->id_opcion)) {
  echo json_encode(["status" => "error", "message" => "El ID de la opción es obligatorio"]);
  exit;
}

$stmt = $db->prepare("DELETE FROM Opcion_Respuesta WHERE id_opcion=?");
$stmt->bind_param("i", $obj->id_opcion);

if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Opción eliminada correctamente"]);
  } else {
    echo json_encode(["status" => "warning", "message" => "No se encontró la opción con el ID proporcionado"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Error al eliminar la opción: " . $stmt->error]);
}

$stmt->close();
