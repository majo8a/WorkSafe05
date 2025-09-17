<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_usuario_doc, id_usuario, id_documento, fecha_asignacion, tipo_acceso, firmado, fecha_firma FROM Usuario_Documento");
$stmt->execute();
$stmt->bind_result($id_usuario_doc, $id_usuario, $id_documento, $fecha_asignacion, $tipo_acceso, $firmado, $fecha_firma);

$arr = array();
while ($stmt->fetch()) {
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
