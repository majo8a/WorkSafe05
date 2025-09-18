<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_resultado, id_evaluacion, categoria, dominio, dimension, puntaje_obtenido, nivel_riesgo, interpretacion, id_rango 
                      FROM Resultado");
$stmt->execute();
$stmt->bind_result($id_resultado, $id_evaluacion, $categoria, $dominio, $dimension, $puntaje_obtenido, $nivel_riesgo, $interpretacion, $id_rango);

$arr = array();
while ($stmt->fetch()) {
  $arr[] = array(
    'id_resultado' => $id_resultado,
    'id_evaluacion' => $id_evaluacion,
    'categoria' => $categoria,
    'dominio' => $dominio,
    'dimension' => $dimension,
    'puntaje_obtenido' => $puntaje_obtenido,
    'nivel_riesgo' => $nivel_riesgo,
    'interpretacion' => $interpretacion,
    'id_rango' => $id_rango
  );
}

$stmt->close();
echo json_encode($arr);
