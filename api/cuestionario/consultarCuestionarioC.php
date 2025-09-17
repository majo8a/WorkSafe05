<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("
    SELECT c.id_cuestionario, c.nombre, c.descripcion, c.version, c.estado, c.fecha_creacion, 
           c.id_usuario_creador, u.nombre_completo as nombre_creador
    FROM Cuestionario c
    LEFT JOIN Usuario u ON c.id_usuario_creador = u.id_usuario
");
$stmt->execute();
$result = $stmt->get_result();

$cuestionarios = [];
while ($row = $result->fetch_assoc()) {
  $cuestionarios[] = $row;
}

$stmt->close();

echo json_encode($cuestionarios);
