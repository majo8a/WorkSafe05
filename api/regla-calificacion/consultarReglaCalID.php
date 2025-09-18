<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// JSON de entrada
$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_regla, id_cuestionario, dimension, rango_inferior, rango_superior, nivel_riesgo, descripcion 
                      FROM Regla_Calificacion 
                      WHERE id_regla = ?");
$stmt->bind_param('i', $obj->id_regla);
$stmt->execute();
$stmt->bind_result($id_regla, $id_cuestionario, $dimension, $rango_inferior, $rango_superior, $nivel_riesgo, $descripcion);

$arr = array();
if ($stmt->fetch()) {
  $arr[] = array(
    'id_regla' => $id_regla,
    'id_cuestionario' => $id_cuestionario,
    'dimension' => $dimension,
    'rango_inferior' => $rango_inferior,
    'rango_superior' => $rango_superior,
    'nivel_riesgo' => $nivel_riesgo,
    'descripcion' => $descripcion
  );
}

$stmt->close();
echo json_encode($arr);
