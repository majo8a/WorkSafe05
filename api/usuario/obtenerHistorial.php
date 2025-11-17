<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

require_once '../conexion.php';

if (!isset($_GET['id_usuario'])) {
  echo json_encode(['error' => 'Falta id_usuario']);
  exit;
}

$id_usuario = intval($_GET['id_usuario']);

/*
   Traemos SOLO datos de Evaluacion
   + el nombre del cuestionario mediante JOIN
*/
$sql = "
    SELECT 
        e.id_evaluacion,
        c.nombre AS nombre_evaluacion,
        e.estado,
        e.fecha_aplicacion
    FROM Evaluacion e
    INNER JOIN Cuestionario c ON c.id_cuestionario = e.id_cuestionario
    WHERE e.id_usuario = ?
    ORDER BY e.fecha_aplicacion DESC
";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $id_usuario);

$stmt->execute();
$stmt->bind_result($id_evaluacion, $nombre_evaluacion, $estado, $fecha_aplicacion);

$lista = [];
while ($stmt->fetch()) {
  $lista[] = [
    'id_evaluacion'    => $id_evaluacion,
    'nombre_evaluacion' => $nombre_evaluacion,
    'estado'           => $estado,
    'fecha_aplicacion' => $fecha_aplicacion
  ];
}

$stmt->close();
echo json_encode($lista, JSON_UNESCAPED_UNICODE);
