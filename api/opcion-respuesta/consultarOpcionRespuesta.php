<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Preparar la consulta
$stmt = $db->prepare("SELECT id_opcion, etiqueta, valor FROM Opcion_Respuesta");
$stmt->execute();
$stmt->bind_result($id_opcion, $etiqueta, $valor);

// Crear el array de resultados
$arr = array();
while ($stmt->fetch()) {
  $arr[] = array(
    'id_opcion' => $id_opcion,
    'etiqueta' => $etiqueta,
    'valor' => $valor
  );
}

// Cerrar la consulta y devolver JSON
$stmt->close();
echo json_encode($arr);
