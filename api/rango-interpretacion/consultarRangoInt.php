<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_rango, tipo, objeto, rango_inferior, rango_superior, nivel_riesgo, descripcion 
                      FROM Rango_Interpretacion");
$stmt->execute();
$stmt->bind_result($id_rango, $tipo, $objeto, $rango_inferior, $rango_superior, $nivel_riesgo, $descripcion);

$arr = array();
while ($stmt->fetch()) {
  $arr[] = array(
    'id_rango' => $id_rango,
    'tipo' => $tipo,
    'objeto' => $objeto,
    'rango_inferior' => $rango_inferior,
    'rango_superior' => $rango_superior,
    'nivel_riesgo' => $nivel_riesgo,
    'descripcion' => $descripcion
  );
}

$stmt->close();
echo json_encode($arr);
