<?php
require_once '../conexion.php';

$idEvaluacion = $_POST['idEvaluacion'];

$db->query("DELETE FROM Respuesta WHERE id_evaluacion = $idEvaluacion");
$db->query("UPDATE Evaluacion SET estado = 'pendiente' WHERE id_evaluacion = $idEvaluacion");

echo json_encode(['success' => true]);
