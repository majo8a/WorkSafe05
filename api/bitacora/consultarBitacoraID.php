<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_bitacora, id_usuario, accion, modulo, descripcion, fecha_evento, objeto, id_objeto, ip_origen 
                      FROM Bitacora 
                      WHERE id_bitacora = ?");
$stmt->bind_param('i', $obj->id_bitacora);
$stmt->execute();
$stmt->bind_result($id_bitacora, $id_usuario, $accion, $modulo, $descripcion, $fecha_evento, $objeto, $id_objeto, $ip_origen);

$arr = array();
if ($stmt->fetch()) {
    $arr[] = array(
        'id_bitacora' => $id_bitacora,
        'id_usuario' => $id_usuario,
        'accion' => $accion,
        'modulo' => $modulo,
        'descripcion' => $descripcion,
        'fecha_evento' => $fecha_evento,
        'objeto' => $objeto,
        'id_objeto' => $id_objeto,
        'ip_origen' => $ip_origen
    );
}

$stmt->close();
echo json_encode($arr);
?>
