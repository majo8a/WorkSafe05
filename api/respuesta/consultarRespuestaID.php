<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_respuesta, id_pregunta, id_evaluacion, id_opcion_respuesta_select, fecha_respuesta 
                      FROM Respuesta 
                      WHERE id_respuesta = ?");
$stmt->bind_param('i', $obj->id_respuesta);
$stmt->execute();
$stmt->bind_result($id_respuesta, $id_pregunta, $id_evaluacion, $id_opcion_respuesta_select, $fecha_respuesta);

$arr = array();
if ($stmt->fetch()) {
    $arr[] = array(
        'id_respuesta' => $id_respuesta,
        'id_pregunta' => $id_pregunta,
        'id_evaluacion' => $id_evaluacion,
        'id_opcion_respuesta_select' => $id_opcion_respuesta_select,
        'fecha_respuesta' => $fecha_respuesta
    );
}

$stmt->close();
echo json_encode($arr);
