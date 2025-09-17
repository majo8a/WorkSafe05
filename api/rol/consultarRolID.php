<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Obtiene el JSON enviado por POST
$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_rol, nombre_rol, descripcion 
                      FROM Rol 
                      WHERE id_rol = ?");
$stmt->bind_param('i', $obj->id_rol);
$stmt->execute();
$stmt->bind_result($id_rol, $nombre_rol, $descripcion);

$arr = array();
if ($stmt->fetch()) {
  $arr[] = array(
    'id_rol' => $id_rol,
    'nombre_rol' => $nombre_rol,
    'descripcion' => $descripcion
  );
}

$stmt->close();
echo json_encode($arr);
