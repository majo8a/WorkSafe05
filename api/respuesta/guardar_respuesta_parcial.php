<?php
require_once '../conexion.php';
session_start();

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

/* ===============================
   LEER JSON DE FORMA SEGURA
   =============================== */
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode([
        'success' => false,
        'error' => 'No se recibió JSON válido',
        'raw' => $raw
    ]);
    exit;
}

/* ===============================
   VALIDAR DATOS
   =============================== */
$idUsuario     = $_SESSION['id_usuario'] ?? null;
$idCuestionario = $data['idCuestionario'] ?? null;
$idEvaluacion  = $data['idEvaluacion'] ?? null;
$idPregunta    = $data['idPregunta'] ?? null;
$idOpcion      = $data['idOpcion'] ?? null;
$valor         = $data['valor'] ?? 0;

if (!$idUsuario || !$idCuestionario || !$idPregunta || !$idOpcion) {
    echo json_encode([
        'success' => false,
        'error' => 'Datos incompletos',
        'data' => $data
    ]);
    exit;
}

/* ===============================
   CREAR EVALUACIÓN SI NO EXISTE
   =============================== */
if (!$idEvaluacion) {
    $sql = "INSERT INTO Evaluacion
            (id_usuario, id_cuestionario, fecha_aplicacion, estado)
            VALUES (?, ?, NOW(), 'pendiente')";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ii', $idUsuario, $idCuestionario);
    $stmt->execute();
    $idEvaluacion = $stmt->insert_id;
}

/* ===============================
   GUARDAR / ACTUALIZAR RESPUESTA
   =============================== */
$sql = "SELECT id_respuesta
        FROM Respuesta
        WHERE id_evaluacion = ? AND id_pregunta = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('ii', $idEvaluacion, $idPregunta);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $sql = "UPDATE Respuesta
            SET id_opcion_respuesta_select = ?, valor = ?, fecha_respuesta = NOW()
            WHERE id_evaluacion = ? AND id_pregunta = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('iiii', $idOpcion, $valor, $idEvaluacion, $idPregunta);
} else {
    $sql = "INSERT INTO Respuesta
            (id_pregunta, id_evaluacion, id_opcion_respuesta_select, valor, fecha_respuesta)
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('iiii', $idPregunta, $idEvaluacion, $idOpcion, $valor);
}

$stmt->execute();

/* ===============================
   RESPUESTA FINAL
   =============================== */
echo json_encode([
    'success' => true,
    'idEvaluacion' => $idEvaluacion
]);
