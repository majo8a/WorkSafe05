<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Recibir datos JSON
$obj = json_decode(file_get_contents("php://input"));

// Validar ID
if (!isset($obj->id_usuario) || !is_numeric($obj->id_usuario)) {
  echo json_encode([
    "status" => "error",
    "message" => "El ID del usuario es obligatorio y debe ser numérico"
  ]);
  exit;
}

// Preparar DELETE
$stmt = $db->prepare("DELETE FROM Usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $obj->id_usuario);

if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
    echo json_encode([
      "status" => "success",
      "message" => "Usuario eliminado correctamente"
    ]);
  } else {
    echo json_encode([
      "status" => "warning",
      "message" => "No se encontró el usuario con el ID proporcionado"
    ]);
  }
} else {
  echo json_encode([
    "status" => "error",
    "message" => "Error al eliminar el usuario: " . $stmt->error
  ]);
}

$stmt->close();
