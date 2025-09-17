<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_notificacion, tipo, contenido, fecha_envio, estado_general, modulo_origen FROM Notificacion");
$stmt->execute();
$stmt->bind_result($id_notificacion, $tipo, $contenido, $fecha_envio, $estado_general, $modulo_origen);

$arr = array();
while ($stmt->fetch()) {
    $arr[] = array(
        'id_notificacion' => $id_notificacion,
        'tipo' => $tipo,
        'contenido' => $contenido,
        'fecha_envio' => $fecha_envio,
        'estado_general' => $estado_general,
        'modulo_origen' => $modulo_origen
    );
}

$stmt->close();
echo json_encode($arr);
?>
