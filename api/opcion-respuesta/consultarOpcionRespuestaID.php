<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Obtiene el JSON enviado por POST
$obj = json_decode(file_get_contents("php://input"));

// Preparar la consulta con filtro por id_opcion
$stmt = $db->prepare("SELECT id_opcion, etiqueta, valor 
                      FROM Opcion_Respuesta 
                      WHERE id_opcion = ?");
$stmt->bind_param('i', $obj->id_opcion);
$stmt->execute();
$stmt->bind_result($id_opcion, $etiqueta, $valor);

$arr = array();
if ($stmt->fetch()) {
  $arr[] = array(
    'id_opcion' => $id_opcion,
    'etiqueta' => $etiqueta,
    'valor' => $valor
  );
}

$stmt->close();
echo json_encode($arr);
