<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_documento, titulo, descripcion, ruta_archivo, fecha_publicacion, id_usuario_publicador, acceso_roles 
                      FROM Documento 
                      WHERE id_documento = ?");
$stmt->bind_param('i', $obj->id_documento);
$stmt->execute();
$stmt->bind_result($id_documento, $titulo, $descripcion, $ruta_archivo, $fecha_publicacion, $id_usuario_publicador, $acceso_roles);

$arr = array();
if ($stmt->fetch()) {
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
