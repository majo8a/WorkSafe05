<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_usuario_notificacion, id_usuario, id_notificacion, estado, fecha_visualizacion 
                      FROM Usuario_Notificacion 
                      WHERE id_usuario_notificacion = ?");
$stmt->bind_param('i', $obj->id_usuario_notificacion);
$stmt->execute();
$stmt->bind_result($id_usuario_notificacion, $id_usuario, $id_notificacion, $estado, $fecha_visualizacion);

$arr = array();
if ($stmt->fetch()) {
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
