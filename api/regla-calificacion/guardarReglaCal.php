<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("INSERT INTO Regla_Calificacion (id_cuestionario, dimension, rango_inferior, rango_superior, nivel_riesgo, descripcion) 
                      VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
  'isiiss',
  $obj->id_cuestionario,
  $obj->dimension,
  $obj->rango_inferior,
  $obj->rango_superior,
  $obj->nivel_riesgo,
  $obj->descripcion
);

$response = array();
if ($stmt->execute()) {
  $response['status'] = 'success';
  $response['id_insertado'] = $stmt->insert_id;
} else {
  $response['status'] = 'error';
  $response['message'] = $stmt->error;
}

$stmt->close();
echo json_encode($response);
