<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_usuario_doc, id_usuario, id_documento, fecha_asignacion, tipo_acceso, firmado, fecha_firma 
                      FROM Usuario_Documento 
                      WHERE id_usuario_doc = ?");
$stmt->bind_param('i', $obj->id_usuario_doc);
$stmt->execute();
$stmt->bind_result($id_usuario_doc, $id_usuario, $id_documento, $fecha_asignacion, $tipo_acceso, $firmado, $fecha_firma);

$arr = array();
if ($stmt->fetch()) {
    $arr[] = array(
        'id_usuario_doc' => $id_usuario_doc,
        'id_usuario' => $id_usuario,
        'id_documento' => $id_documento,
        'fecha_asignacion' => $fecha_asignacion,
        'tipo_acceso' => $tipo_acceso,
        'firmado' => $firmado,
        'fecha_firma' => $fecha_firma
    );
}

$stmt->close();
echo json_encode($arr);
?>
