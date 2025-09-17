<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Preparar la consulta
$stmt = $db->prepare("SELECT id_evaluacion, id_usuario, id_cuestionario, fecha_aplicacion, estado, id_usuario_aplicador 
                      FROM Evaluacion");
$stmt->execute();
$stmt->bind_result($id_evaluacion, $id_usuario, $id_cuestionario, $fecha_aplicacion, $estado, $id_usuario_aplicador);

$arr = array();
while ($stmt->fetch()) {
    $arr[] = array(
        'id_evaluacion' => $id_evaluacion,
        'id_usuario' => $id_usuario,
        'id_cuestionario' => $id_cuestionario,
        'fecha_aplicacion' => $fecha_aplicacion,
        'estado' => $estado,
        'id_usuario_aplicador' => $id_usuario_aplicador
    );
}

$stmt->close();
echo json_encode($arr);
