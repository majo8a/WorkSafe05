<?php
require_once '../conexion.php';

$idEvaluacion = $_GET['idEvaluacion'] ?? null;

if (!$idEvaluacion) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT id_pregunta, id_opcion_respuesta_select
    FROM Respuesta
    WHERE id_evaluacion = ?
";

$stmt = $db->prepare($sql);
$stmt->bind_param('i', $idEvaluacion);
$stmt->execute();
$result = $stmt->get_result();

$respuestas = [];

while ($row = $result->fetch_assoc()) {
    // CLAVE: id_pregunta => id_opcion
    $respuestas[$row['id_pregunta']] = $row['id_opcion_respuesta_select'];
}

echo json_encode($respuestas);
