<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_opcion, etiqueta, valor FROM Opcion_Respuesta");
$stmt->execute();
$result = $stmt->get_result();

$opciones = [];
while ($row = $result->fetch_assoc()) {
  $opciones[] = $row;
}

$stmt->close();
echo json_encode($opciones);
