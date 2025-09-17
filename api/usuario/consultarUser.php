<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_usuario, nombre_completo, correo, telefono, password_hash, autenticacion_dos_factores, activo, id_rol, fecha_registro FROM Usuario");
$stmt->execute();
$stmt->bind_result($id_usuario, $nombre_completo, $correo, $telefono, $password_hash, $autenticacion_dos_factores, $activo, $id_rol, $fecha_registro);

$arr = array();
while ($stmt->fetch()) {
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
