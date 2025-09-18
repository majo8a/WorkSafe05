<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_rango, tipo, objeto, rango_inferior, rango_superior, nivel_riesgo, descripcion 
                      FROM Rango_Interpretacion 
                      WHERE id_rango = ?");
$stmt->bind_param('i', $obj->id_rango);
$stmt->execute();
$stmt->bind_result($id_rango, $tipo, $objeto, $rango_inferior, $rango_superior, $nivel_riesgo, $descripcion);

$arr = array();
if ($stmt->fetch()) {
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
