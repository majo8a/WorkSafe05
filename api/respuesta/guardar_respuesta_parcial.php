<?php
require_once '../conexion.php';
session_start();

$data = json_decode(file_get_contents("php://input"), true);

$idUsuario = $_SESSION['id_usuario'];
$idCuestionario = $data['idCuestionario'];
$idPregunta = $data['idPregunta'];
$idOpcion = $data['idOpcion'];
$valor = $data['valor'];
$idEvaluacion = $data['idEvaluacion'] ?? null;

// 1️⃣ Crear evaluación si no existe
if (!$idEvaluacion) {
    $sql = "INSERT INTO Evaluacion (id_usuario, id_cuestionario, fecha_aplicacion, estado)
            VALUES (?, ?, NOW(), 'pendiente')";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $idUsuario, $idCuestionario);
    $stmt->execute();
    $idEvaluacion = $stmt->insert_id;
}

// 2️⃣ Verificar si ya existe respuesta
$sql = "SELECT id_respuesta 
        FROM Respuesta 
        WHERE id_evaluacion = ? AND id_pregunta = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ii", $idEvaluacion, $idPregunta);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    // UPDATE
    $sql = "UPDATE Respuesta
            SET id_opcion_respuesta_select = ?, valor = ?, fecha_respuesta = NOW()
            WHERE id_respuesta = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iii", $idOpcion, $valor, $row['id_respuesta']);
    $stmt->execute();
} else {
    // INSERT
    $sql = "INSERT INTO Respuesta
            (id_pregunta, id_evaluacion, id_opcion_respuesta_select, valor, fecha_respuesta)
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iiii", $idPregunta, $idEvaluacion, $idOpcion, $valor);
    $stmt->execute();
}

echo json_encode(['idEvaluacion' => $idEvaluacion]);
