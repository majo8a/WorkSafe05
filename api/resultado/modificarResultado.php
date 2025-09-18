<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Obtiene el JSON enviado por POST
$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Resultado 
                      SET id_evaluacion = ?, categoria = ?, dominio = ?, dimension = ?, puntaje_obtenido = ?, nivel_riesgo = ?, interpretacion = ?, id_rango = ? 
                      WHERE id_resultado = ?");
$stmt->bind_param(
  'isssissii',
  $obj->id_evaluacion,
  $obj->categoria,
  $obj->dominio,
  $obj->dimension,
  $obj->puntaje_obtenido,
  $obj->nivel_riesgo,
  $obj->interpretacion,
  $obj->id_rango,
  $obj->id_resultado
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
