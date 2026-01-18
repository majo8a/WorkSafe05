<?php
require_once '../conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$idEvaluacion = $data['idEvaluacion'];

$sql = "UPDATE Evaluacion
        SET estado = 'completado'
        WHERE id_evaluacion = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $idEvaluacion);
$stmt->execute();

echo json_encode(['success' => true]);
