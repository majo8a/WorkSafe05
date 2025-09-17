<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_capacitacion, tema, descripcion, fecha_inicio, fecha_fin, tipo_modalidad, id_usuario_asignador FROM Capacitacion");
$stmt->execute();
$stmt->bind_result($id_capacitacion, $tema, $descripcion, $fecha_inicio, $fecha_fin, $tipo_modalidad, $id_usuario_asignador);

$arr = array();
while ($stmt->fetch()) {
    $arr[] = array(
        'id_capacitacion' => $id_capacitacion,
        'tema' => $tema,
        'descripcion' => $descripcion,
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin,
        'tipo_modalidad' => $tipo_modalidad,
        'id_usuario_asignador' => $id_usuario_asignador
    );
}

$stmt->close();
echo json_encode($arr);
?>
