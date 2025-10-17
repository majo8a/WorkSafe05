<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "error" => "Sesión no iniciada."]);
    exit;
}

$idUsuario = $_SESSION['id'];
$data = json_decode(file_get_contents("php://input"), true);

$idCuestionario = $data['idCuestionario'] ?? null;
$respuestas = $data['respuestas'] ?? [];

if (!$idCuestionario || empty($respuestas)) {
    echo json_encode(["success" => false, "error" => "Datos incompletos."]);
    exit;
}

try {
    // Verificar si ya respondió
    $sqlCheck = "SELECT id_evaluacion FROM Evaluacion 
                 WHERE id_usuario = ? AND id_cuestionario = ? AND estado = 'completado' LIMIT 1";
    $stmtCheck = $db->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $idUsuario, $idCuestionario);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "Ya has respondido este cuestionario."]);
        exit;
    }

    // Crear evaluación
    $sqlEval = "INSERT INTO Evaluacion (id_usuario, id_cuestionario, fecha_aplicacion, estado)
                VALUES (?, ?, NOW(), 'completado')";
    $stmtEval = $db->prepare($sqlEval);
    $stmtEval->bind_param("ii", $idUsuario, $idCuestionario);
    $stmtEval->execute();
    $idEvaluacion = $stmtEval->insert_id;

    // Traer todas las opciones de cada pregunta
    $sqlOpciones = "SELECT id_pregunta, id_opcion, valor FROM Opcion_Respuesta 
                    WHERE id_pregunta IN (SELECT id_pregunta FROM Pregunta WHERE id_cuestionario = ?)";
    $stmtOpciones = $db->prepare($sqlOpciones);
    $stmtOpciones->bind_param("i", $idCuestionario);
    $stmtOpciones->execute();
    $resOpciones = $stmtOpciones->get_result();

    $opcionesPorPregunta = [];
    while ($row = $resOpciones->fetch_assoc()) {
        $opcionesPorPregunta[$row['id_pregunta']][$row['id_opcion']] = $row['valor'];
    }

    // Insertar respuestas
    $sqlResp = "INSERT INTO Respuesta (id_pregunta, id_evaluacion, id_opcion_respuesta_select, valor, fecha_respuesta)
                VALUES (?, ?, ?, ?, NOW())";
    $stmtResp = $db->prepare($sqlResp);

    $sqlPreguntas = "SELECT id_pregunta FROM Pregunta WHERE id_cuestionario = ? ORDER BY orden ASC";
    $stmtPreg = $db->prepare($sqlPreguntas);
    $stmtPreg->bind_param("i", $idCuestionario);
    $stmtPreg->execute();
    $resultPreg = $stmtPreg->get_result();
    $preguntas = [];
    while ($row = $resultPreg->fetch_assoc()) {
        $preguntas[] = $row['id_pregunta'];
    }

    foreach ($respuestas as $i => $idOpcion) {
        $idPregunta = $preguntas[$i] ?? null;
        if (!$idPregunta) continue;

        $valorOpcion = $opcionesPorPregunta[$idPregunta][$idOpcion] ?? null;
        if ($valorOpcion === null) continue;

        $stmtResp->bind_param("iiii", $idPregunta, $idEvaluacion, $idOpcion, $valorOpcion);
        $stmtResp->execute();
    }

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
