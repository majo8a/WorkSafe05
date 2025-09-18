<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Obtiene el JSON enviado por POST
$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("DELETE FROM Resultado WHERE id_resultado = ?");
$stmt->bind_param('i', $obj->id_resultado);

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
