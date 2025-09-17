<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_rol, nombre_rol, descripcion FROM Rol");
$stmt->execute();
$stmt->bind_result($id_rol, $nombre_rol, $descripcion);

$arr = array();
while ($stmt->fetch()) {
  $arr[] = array(
    'id_rol' => $id_rol,
    'nombre_rol' => $nombre_rol,
    'descripcion' => $descripcion
  );
}

$stmt->close();
echo json_encode($arr);
