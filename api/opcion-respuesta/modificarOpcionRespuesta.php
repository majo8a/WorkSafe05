<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

// Validaciones
if (!isset($obj->id_opcion) || !is_numeric($obj->id_opcion)) {
  echo json_encode(["status" => "error", "message" => "El ID de la opción es obligatorio"]);
  exit;
}
if (!isset($obj->etiqueta) || empty(trim($obj->etiqueta))) {
  echo json_encode(["status" => "error", "message" => "La etiqueta es obligatoria"]);
  exit;
}
if (!isset($obj->valor) || !is_numeric($obj->valor)) {
  echo json_encode(["status" => "error", "message" => "El valor es obligatorio y debe ser numérico"]);
  exit;
}

// Preparar UPDATE
$stmt = $db->prepare("UPDATE Opcion_Respuesta SET etiqueta=?, valor=? WHERE id_opcion=?");
$stmt->bind_param("sii", $obj->etiqueta, $obj->valor, $obj->id_opcion);

if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Opción modificada correctamente"]);
  } else {
    echo json_encode(["status" => "warning", "message" => "No se encontraron cambios o la opción no existe"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Error al modificar la opción: " . $stmt->error]);
}

$stmt->close();
