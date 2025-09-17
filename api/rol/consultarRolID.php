<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

// Validar ID
if (!isset($obj->id_rol) || !is_numeric($obj->id_rol)) {
  echo json_encode([
    "status" => "error",
    "message" => "El ID del rol es obligatorio y debe ser numérico"
  ]);
  exit;
}

$stmt = $db->prepare("SELECT id_rol, nombre_rol, descripcion FROM Rol WHERE id_rol = ?");
$stmt->bind_param("i", $obj->id_rol);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode($row);
} else {
  echo json_encode([
    "status" => "error",
    "message" => "Rol no encontrado"
  ]);
}

$stmt->close();
