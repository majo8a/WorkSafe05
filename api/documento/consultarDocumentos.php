<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_documento, titulo, descripcion, ruta_archivo, fecha_publicacion, id_usuario_publicador, acceso_roles FROM Documento");
$stmt->execute();
$stmt->bind_result($id_documento, $titulo, $descripcion, $ruta_archivo, $fecha_publicacion, $id_usuario_publicador, $acceso_roles);

$arr = array();
while ($stmt->fetch()) {
    $arr[] = array(
        'id_documento' => $id_documento,
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'ruta_archivo' => $ruta_archivo,
        'fecha_publicacion' => $fecha_publicacion,
        'id_usuario_publicador' => $id_usuario_publicador,
        'acceso_roles' => $acceso_roles
    );
}

$stmt->close();
echo json_encode($arr);
?>
