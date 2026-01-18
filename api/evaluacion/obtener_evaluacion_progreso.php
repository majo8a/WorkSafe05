<?php
require_once '../conexion.php';
session_start();

$idUsuario = $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
$idCuestionario = $_GET['idCuestionario'] ?? null;

if (!$idUsuario || !$idCuestionario) {
    echo json_encode(['existe' => false]);
    exit;
}

$sql = "
    SELECT id_evaluacion 
    FROM Evaluacion
    WHERE id_usuario = ?
      AND id_cuestionario = ?
      AND estado = 'pendiente'
    LIMIT 1
";

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
