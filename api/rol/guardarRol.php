<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Recibir datos JSON
$obj = json_decode(file_get_contents("php://input"));

// Validar que lleguen los parámetros necesarios
if (!isset($obj->nombre_rol) || empty(trim($obj->nombre_rol))) {
  echo json_encode([
    "status" => "error",
    "message" => "El nombre del rol es obligatorio"
  ]);
  exit;
}

// Preparar INSERT
$stmt = $db->prepare("INSERT INTO Rol (nombre_rol, descripcion) VALUES (?, ?)");
$stmt->bind_param("ss", $obj->nombre_rol, $obj->descripcion);

if ($stmt->execute()) {
  echo json_encode([
    "status" => "success",
    "message" => "Rol guardado correctamente",
    "id_rol" => $stmt->insert_id
  ]);
} else {
  echo json_encode([
    "status" => "error",
    "message" => "Error al guardar el rol: " . $stmt->error
  ]);
}

$stmt->close();
