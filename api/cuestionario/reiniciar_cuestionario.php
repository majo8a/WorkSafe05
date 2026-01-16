<?php
require_once '../conexion.php';
session_start();

$idUsuario = $_SESSION['id_usuario'];
$idEvaluacion = intval($_POST['idEvaluacion']);

$db->begin_transaction();

try {
    // Borrar respuestas
    $sql = "DELETE FROM Respuesta WHERE id_evaluacion = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $idEvaluacion);
    $stmt->execute();

    // Reiniciar evaluación
    $sql = "UPDATE Evaluacion 
            SET fecha_aplicacion = NOW(), estado = 'pendiente'
            WHERE id_evaluacion = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $idEvaluacion);
    $stmt->execute();

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
