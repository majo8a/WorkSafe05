<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

// Validar ID
if (!isset($obj->id_usuario) || !is_numeric($obj->id_usuario)) {
  echo json_encode([
    "status" => "error",
    "message" => "El ID del usuario es obligatorio y debe ser numérico"
  ]);
  exit;
}

// Preparar SELECT
$stmt = $db->prepare("
    SELECT u.id_usuario, u.nombre_completo, u.correo, u.telefono, 
           u.autenticacion_dos_factores, u.activo, u.id_rol, r.nombre_rol, u.fecha_registro
    FROM Usuario u
    LEFT JOIN Rol r ON u.id_rol = r.id_rol
    WHERE u.id_usuario = ?
");
$stmt->bind_param("i", $obj->id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode($row);
} else {
  echo json_encode([
    "status" => "error",
    "message" => "Usuario no encontrado"
  ]);
}

$stmt->close();
