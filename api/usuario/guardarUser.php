<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Recibir datos JSON
$obj = json_decode(file_get_contents("php://input"));

// Validaciones mínimas
if (!isset($obj->nombre_completo) || empty(trim($obj->nombre_completo))) {
  echo json_encode([
    "status" => "error",
    "message" => "El nombre completo es obligatorio"
  ]);
  exit;
}

if (!isset($obj->correo) || empty(trim($obj->correo))) {
  echo json_encode([
    "status" => "error",
    "message" => "El correo es obligatorio"
  ]);
  exit;
}

if (!isset($obj->password) || empty(trim($obj->password))) {
  echo json_encode([
    "status" => "error",
    "message" => "La contraseña es obligatoria"
  ]);
  exit;
}

if (!isset($obj->id_rol) || !is_numeric($obj->id_rol)) {
  echo json_encode([
    "status" => "error",
    "message" => "El rol del usuario es obligatorio"
  ]);
  exit;
}

// Encriptar la contraseña
$password_hash = password_hash($obj->password, PASSWORD_BCRYPT);

// Valores opcionales
$telefono = isset($obj->telefono) ? $obj->telefono : null;
$autenticacion_dos_factores = isset($obj->autenticacion_dos_factores) ? (int)$obj->autenticacion_dos_factores : 0;

// Preparar INSERT
$stmt = $db->prepare("INSERT INTO Usuario 
    (nombre_completo, correo, telefono, password_hash, autenticacion_dos_factores, id_rol) 
    VALUES (?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
  "ssssii",
  $obj->nombre_completo,
  $obj->correo,
  $telefono,
  $password_hash,
  $autenticacion_dos_factores,
  $obj->id_rol
);

if ($stmt->execute()) {
  echo json_encode([
    "status" => "success",
    "message" => "Usuario guardado correctamente",
    "id_usuario" => $stmt->insert_id
  ]);
} else {
  echo json_encode([
    "status" => "error",
    "message" => "Error al guardar el usuario: " . $stmt->error
  ]);
}

$stmt->close();
