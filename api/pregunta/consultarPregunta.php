<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("
    SELECT p.*, c.nombre as nombre_cuestionario
    FROM Pregunta p
    LEFT JOIN Cuestionario c ON p.id_cuestionario = c.id_cuestionario
");
$stmt->execute();
$result = $stmt->get_result();

$preguntas = [];
while ($row = $result->fetch_assoc()) {
  $preguntas[] = $row;
}

$stmt->close();

echo json_encode($preguntas);
