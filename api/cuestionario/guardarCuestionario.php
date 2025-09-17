<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

// Validaciones
if (!isset($obj->nombre) || empty(trim($obj->nombre))) {
  echo json_encode(["status" => "error", "message" => "El nombre del cuestionario es obligatorio"]);
  exit;
}
if (!isset($obj->descripcion) || empty(trim($obj->descripcion))) {
  echo json_encode(["status" => "error", "message" => "La descripción es obligatoria"]);
  exit;
}
if (!isset($obj->version) || empty(trim($obj->version))) {
  echo json_encode(["status" => "error", "message" => "La versión es obligatoria"]);
  exit;
}
if (!isset($obj->id_usuario_creador) || !is_numeric($obj->id_usuario_creador)) {
  echo json_encode(["status" => "error", "message" => "El ID del creador es obligatorio"]);
  exit;
}

// Fecha actual
$fecha_creacion = date("Y-m-d H:i:s");

// Preparar INSERT
$stmt = $db->prepare("INSERT INTO Cuestionario (nombre, descripcion, version, fecha_creacion, id_usuario_creador) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $obj->nombre, $obj->descripcion, $obj->version, $fecha_creacion, $obj->id_usuario_creador);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Cuestionario guardado correctamente", "id_cuestionario" => $stmt->insert_id]);
} else {
  echo json_encode(["status" => "error", "message" => "Error al guardar el cuestionario: " . $stmt->error]);
}

$stmt->close();
