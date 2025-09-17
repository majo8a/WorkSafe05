<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_capacitacion, tema, descripcion, fecha_inicio, fecha_fin, tipo_modalidad, id_usuario_asignador 
                      FROM Capacitacion 
                      WHERE id_capacitacion = ?");
$stmt->bind_param('i', $obj->id_capacitacion);
$stmt->execute();
$stmt->bind_result($id_capacitacion, $tema, $descripcion, $fecha_inicio, $fecha_fin, $tipo_modalidad, $id_usuario_asignador);

$arr = array();
if ($stmt->fetch()) {
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
