<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Recibir datos JSON
$obj = json_decode(file_get_contents("php://input"));

// Validar que lleguen los parámetros necesarios
if (!isset($obj->id_rol) || !is_numeric($obj->id_rol)) {
  echo json_encode([
    "status" => "error",
    "message" => "El ID del rol es obligatorio y debe ser numérico"
  ]);
  exit;
}

if (!isset($obj->nombre_rol) || empty(trim($obj->nombre_rol))) {
  echo json_encode([
    "status" => "error",
    "message" => "El nombre del rol es obligatorio"
  ]);
  exit;
}

// Preparar UPDATE
$stmt = $db->prepare("UPDATE Rol SET nombre_rol = ?, descripcion = ? WHERE id_rol = ?");
$stmt->bind_param("ssi", $obj->nombre_rol, $obj->descripcion, $obj->id_rol);

if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
    echo json_encode([
      "status" => "success",
      "message" => "Rol modificado correctamente"
    ]);
  } else {
    echo json_encode([
      "status" => "warning",
      "message" => "No se encontró el rol o no hubo cambios"
    ]);
  }
} else {
  echo json_encode([
    "status" => "error",
    "message" => "Error al modificar el rol: " . $stmt->error
  ]);
}

$stmt->close();
