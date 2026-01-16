<?php
require_once '../conexion.php';

$idEvaluacion = intval($_GET['idEvaluacion']);

$sql = "SELECT id_pregunta, id_opcion_respuesta_select
        FROM Respuesta
        WHERE id_evaluacion = ?";

$stmt = $db->prepare($sql);
$stmt->bind_param('i', $idEvaluacion);
$stmt->execute();
$res = $stmt->get_result();

$respuestas = [];
while ($row = $res->fetch_assoc()) {
    $respuestas[$row['id_pregunta']] = $row['id_opcion_respuesta_select'];
}

echo json_encode($respuestas);
