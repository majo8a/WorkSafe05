<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

// Validaciones
if (!isset($obj->id_cuestionario) || !is_numeric($obj->id_cuestionario)) {
  echo json_encode(["status" => "error", "message" => "El ID del cuestionario es obligatorio"]);
  exit;
}
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
if (!isset($obj->estado) || empty(trim($obj->estado))) {
  $obj->estado = "activo";
}
if (!isset($obj->id_usuario_creador) || !is_numeric($obj->id_usuario_creador)) {
  echo json_encode(["status" => "error", "message" => "El ID del creador es obligatorio"]);
  exit;
}

// Preparar UPDATE
$stmt = $db->prepare("UPDATE Cuestionario SET nombre=?, descripcion=?, version=?, estado=?, id_usuario_creador=? WHERE id_cuestionario=?");
$stmt->bind_param("ssssii", $obj->nombre, $obj->descripcion, $obj->version, $obj->estado, $obj->id_usuario_creador, $obj->id_cuestionario);

if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Cuestionario modificado correctamente"]);
  } else {
    echo json_encode(["status" => "warning", "message" => "No se encontraron cambios o el cuestionario no existe"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Error al modificar el cuestionario: " . $stmt->error]);
}

$stmt->close();
