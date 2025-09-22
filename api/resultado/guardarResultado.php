<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Recibir JSON
$obj = json_decode(file_get_contents("php://input"));

// Asignar null si id_rango no viene
$id_rango = isset($obj->id_rango) ? $obj->id_rango : null;

// Preparar sentencia
$stmt = $db->prepare("INSERT INTO Resultado 
(id_evaluacion, categoria, dominio, dimension, puntaje_obtenido, nivel_riesgo, interpretacion, id_rango)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

// Tipos correctos: i = int, s = string
$stmt->bind_param(
    'isssissi',
    $obj->id_evaluacion,
    $obj->categoria,
    $obj->dominio,
    $obj->dimension,
    $obj->puntaje_obtenido,
    $obj->nivel_riesgo,
    $obj->interpretacion,
    $id_rango
);

// Ejecutar y enviar respuesta
$response = [];
if($stmt->execute()){
    $response['status'] = 'success';
    $response['id_insertado'] = $stmt->insert_id;
}else{
    $response['status'] = 'error';
    $response['message'] = $stmt->error;
}
$stmt->close();
echo json_encode($response);
