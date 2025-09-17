<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Recibir datos JSON
$obj = json_decode(file_get_contents("php://input"));

// Validar ID
if (!isset($obj->id_rol) || !is_numeric($obj->id_rol)) {
  echo json_encode([
    "status" => "error",
    "message" => "El ID del rol es obligatorio y debe ser numérico"
  ]);
  exit;
}

// Preparar DELETE
$stmt = $db->prepare("DELETE FROM Rol WHERE id_rol = ?");
$stmt->bind_param("i", $obj->id_rol);

if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
    echo json_encode([
      "status" => "success",
      "message" => "Rol eliminado correctamente"
    ]);
  } else {
    echo json_encode([
      "status" => "warning",
      "message" => "No se encontró el rol con el ID proporcionado"
    ]);
  }
} else {
  echo json_encode([
    "status" => "error",
    "message" => "Error al eliminar el rol: " . $stmt->error
  ]);
}

$stmt->close();
