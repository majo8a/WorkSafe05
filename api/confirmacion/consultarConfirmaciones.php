<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_confirmacion, id_usuario, id_capacitacion, tipo_confirmacion, fecha_confirmacion, ip_registro, asistio FROM Confirmacion");
$stmt->execute();
$stmt->bind_result($id_confirmacion, $id_usuario, $id_capacitacion, $tipo_confirmacion, $fecha_confirmacion, $ip_registro, $asistio);

$arr = array();
while ($stmt->fetch()) {
    $arr[] = array(
        'id_confirmacion' => $id_confirmacion,
        'id_usuario' => $id_usuario,
        'id_capacitacion' => $id_capacitacion,
        'tipo_confirmacion' => $tipo_confirmacion,
        'fecha_confirmacion' => $fecha_confirmacion,
        'ip_registro' => $ip_registro,
        'asistio' => $asistio
    );
}

$stmt->close();
echo json_encode($arr);
?>
