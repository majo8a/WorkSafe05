<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Regla_Calificacion 
                      SET id_cuestionario = ?, dimension = ?, rango_inferior = ?, rango_superior = ?, nivel_riesgo = ?, descripcion = ? 
                      WHERE id_regla = ?");
$stmt->bind_param(
  'isiissi',
  $obj->id_cuestionario,
  $obj->dimension,
  $obj->rango_inferior,
  $obj->rango_superior,
  $obj->nivel_riesgo,
  $obj->descripcion,
  $obj->id_regla
);

$response = array();
if ($stmt->execute()) {
  $response['status'] = 'success';
  $response['rows_affected'] = $stmt->affected_rows;
} else {
  $response['status'] = 'error';
  $response['message'] = $stmt->error;
}

$stmt->close();
echo json_encode($response);
