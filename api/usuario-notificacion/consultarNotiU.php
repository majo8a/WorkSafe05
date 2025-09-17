<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_usuario_notificacion, id_usuario, id_notificacion, estado, fecha_visualizacion FROM Usuario_Notificacion");
$stmt->execute();
$stmt->bind_result($id_usuario_notificacion, $id_usuario, $id_notificacion, $estado, $fecha_visualizacion);

$arr = array();
while ($stmt->fetch()) {
    $arr[] = array(
        'id_usuario_notificacion' => $id_usuario_notificacion,
        'id_usuario' => $id_usuario,
        'id_notificacion' => $id_notificacion,
        'estado' => $estado,
        'fecha_visualizacion' => $fecha_visualizacion
    );
}

$stmt->close();
echo json_encode($arr);
?>
