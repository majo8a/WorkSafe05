<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Preparar la consulta
$stmt = $db->prepare("SELECT id_respuesta, id_pregunta, id_evaluacion, id_opcion_respuesta_select, fecha_respuesta 
                      FROM Respuesta");
$stmt->execute();
$stmt->bind_result($id_respuesta, $id_pregunta, $id_evaluacion, $id_opcion_respuesta_select, $fecha_respuesta);

$arr = array();
while ($stmt->fetch()) {
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
