<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Obtiene el JSON enviado por POST
$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_usuario, nombre_completo, correo, telefono, password_hash, autenticacion_dos_factores, activo, id_rol, fecha_registro 
                      FROM Usuario 
                      WHERE id_usuario = ?");
$stmt->bind_param('i', $obj->id_usuario);
$stmt->execute();
$stmt->bind_result($id_usuario, $nombre_completo, $correo, $telefono, $password_hash, $autenticacion_dos_factores, $activo, $id_rol, $fecha_registro);

$arr = array();
if ($stmt->fetch()) {
  $arr[] = array(
    'id_usuario' => $id_usuario,
    'nombre_completo' => $nombre_completo,
    'correo' => $correo,
    'telefono' => $telefono,
    'password_hash' => $password_hash,
    'autenticacion_dos_factores' => $autenticacion_dos_factores,
    'activo' => $activo,
    'id_rol' => $id_rol,
    'fecha_registro' => $fecha_registro
  );
}

$stmt->close();
echo json_encode($arr);
