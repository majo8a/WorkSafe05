<?php
require_once '../conexion.php';
session_start();

$idUsuario = $_SESSION['id_usuario'];
$idCuestionario = intval($_GET['idCuestionario']);

// Buscar evaluación pendiente
$sql = "SELECT e.id_evaluacion
FROM Evaluacion e
JOIN Respuesta r ON r.id_evaluacion = e.id_evaluacion
WHERE e.id_usuario = ?
AND e.id_cuestionario = ?
LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param('ii', $idUsuario, $idCuestionario);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    echo json_encode([
        'existe' => true,
        'id_evaluacion' => $row['id_evaluacion']
    ]);
} else {
    echo json_encode(['existe' => false]);
}
