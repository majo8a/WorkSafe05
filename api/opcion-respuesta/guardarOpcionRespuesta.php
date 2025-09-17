<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

// Validaciones
if (!isset($obj->etiqueta) || empty(trim($obj->etiqueta))) {
  echo json_encode(["status" => "error", "message" => "La etiqueta es obligatoria"]);
  exit;
}
if (!isset($obj->valor) || !is_numeric($obj->valor)) {
  echo json_encode(["status" => "error", "message" => "El valor es obligatorio y debe ser numérico"]);
  exit;
}

// Preparar INSERT
$stmt = $db->prepare("INSERT INTO Opcion_Respuesta (etiqueta, valor) VALUES (?, ?)");
$stmt->bind_param("si", $obj->etiqueta, $obj->valor);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Opción guardada correctamente", "id_opcion" => $stmt->insert_id]);
} else {
  echo json_encode(["status" => "error", "message" => "Error al guardar la opción: " . $stmt->error]);
}

$stmt->close();
