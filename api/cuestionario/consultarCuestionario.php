<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_cuestionario, nombre, descripcion, version, estado, fecha_creacion, id_usuario_creador FROM Cuestionario");
$stmt->execute();
$stmt->bind_result($id_cuestionario, $nombre, $descripcion, $version, $estado, $fecha_creacion, $id_usuario_creador);

$arr = array();
while ($stmt->fetch()) {
  $arr[] = array(
    'id_cuestionario' => $id_cuestionario,
    'nombre' => $nombre,
    'descripcion' => $descripcion,
    'version' => $version,
    'estado' => $estado,
    'fecha_creacion' => $fecha_creacion,
    'id_usuario_creador' => $id_usuario_creador
  );
}

$stmt->close();
echo json_encode($arr);
