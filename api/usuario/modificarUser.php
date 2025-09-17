<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

// Validaciones básicas
if (!isset($obj->id_usuario) || !is_numeric($obj->id_usuario)) {
  echo json_encode(["status" => "error", "message" => "El ID del usuario es obligatorio"]);
  exit;
}

if (!isset($obj->nombre_completo) || empty(trim($obj->nombre_completo))) {
  echo json_encode(["status" => "error", "message" => "El nombre completo es obligatorio"]);
  exit;
}

if (!isset($obj->correo) || empty(trim($obj->correo))) {
  echo json_encode(["status" => "error", "message" => "El correo es obligatorio"]);
  exit;
}

if (!isset($obj->id_rol) || !is_numeric($obj->id_rol)) {
  echo json_encode(["status" => "error", "message" => "El rol es obligatorio"]);
  exit;
}

// Preparar query
if (isset($obj->password) && !empty(trim($obj->password))) {
  $password_hash = password_hash($obj->password, PASSWORD_BCRYPT);
  $stmt = $db->prepare("UPDATE Usuario SET 
        nombre_completo=?, correo=?, telefono=?, password_hash=?, autenticacion_dos_factores=?, activo=?, id_rol=? 
        WHERE id_usuario=?");
  $stmt->bind_param(
    "ssssiiii",
    $obj->nombre_completo,
    $obj->correo,
    $obj->telefono,
    $password_hash,
    $obj->autenticacion_dos_factores,
    $obj->activo,
    $obj->id_rol,
    $obj->id_usuario
  );
} else {
  $stmt = $db->prepare("UPDATE Usuario SET 
        nombre_completo=?, correo=?, telefono=?, autenticacion_dos_factores=?, activo=?, id_rol=? 
        WHERE id_usuario=?");
  $stmt->bind_param(
    "sssiiii",
    $obj->nombre_completo,
    $obj->correo,
    $obj->telefono,
    $obj->autenticacion_dos_factores,
    $obj->activo,
    $obj->id_rol,
    $obj->id_usuario
  );
}

// Ejecutar
if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Usuario modificado correctamente"]);
} else {
  echo json_encode(["status" => "error", "message" => "Error al modificar el usuario: " . $stmt->error]);
}

$stmt->close();
