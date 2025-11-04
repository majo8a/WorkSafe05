<?php
// eliminarPregunta.php
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
require_once '../conexion.php';

// Leer JSON enviado por POST
$obj = json_decode(file_get_contents("php://input"));

if (!$obj || !isset($obj->id_pregunta) || !is_numeric($obj->id_pregunta)) {
    echo json_encode([
        "status" => "error",
        "message" => "El ID de la pregunta es obligatorio y debe ser numérico"
    ]);
    exit;
}

$id_pregunta = (int)$obj->id_pregunta;

try {
    $db->begin_transaction();

    // 1️⃣ Obtener los id_opcion de la pregunta
    $stmtOpc = $db->prepare("SELECT id_opcion FROM opcion_respuesta WHERE id_pregunta=?");
    $stmtOpc->bind_param("i", $id_pregunta);
    $stmtOpc->execute();
    $result = $stmtOpc->get_result();
    $opciones = [];
    while ($row = $result->fetch_assoc()) {
        $opciones[] = $row['id_opcion'];
    }
    $stmtOpc->close();

    // 2️⃣ Eliminar respuestas asociadas a esas opciones
    if (count($opciones) > 0) {
        $in = implode(',', array_fill(0, count($opciones), '?'));
        $types = str_repeat('i', count($opciones));
        $stmtResp = $db->prepare("DELETE FROM respuesta WHERE id_opcion_respuesta_select IN ($in)");
        $stmtResp->bind_param($types, ...$opciones);
        $stmtResp->execute();
        $stmtResp->close();
    }

    // 3️⃣ Eliminar opciones de respuesta
    $stmtOpc = $db->prepare("DELETE FROM opcion_respuesta WHERE id_pregunta=?");
    $stmtOpc->bind_param("i", $id_pregunta);
    $stmtOpc->execute();
    $stmtOpc->close();

    // 4️⃣ Eliminar la pregunta
    $stmt = $db->prepare("DELETE FROM pregunta WHERE id_pregunta=?");
    $stmt->bind_param("i", $id_pregunta);
    $stmt->execute();

    $db->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Pregunta y sus dependencias eliminadas correctamente"
    ]);

} catch (Exception $e) {
    $db->rollback();
    echo json_encode([
        "status" => "error",
        "message" => "Error al eliminar la pregunta: " . $e->getMessage()
    ]);
}

$db->close();
