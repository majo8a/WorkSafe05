<?php
// eliminarCuestionario.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

require_once '../conexion.php';
session_start();

// Usuario para triggers
$idUsuario = $_SESSION['id_usuario'] ?? 1;
$db->query("SET @id_usuario_responsable = $idUsuario");

// Leer JSON enviado por POST
$obj = json_decode(file_get_contents("php://input"));

if (!$obj || !isset($obj->id_cuestionario) || !is_numeric($obj->id_cuestionario)) {
    echo json_encode([
        "status" => "error",
        "message" => "El ID del cuestionario es obligatorio y debe ser numérico"
    ]);
    exit;
}

$id_cuestionario = (int)$obj->id_cuestionario;

try {
    $db->begin_transaction();

    // 1️⃣ Verificar existencia del cuestionario
    $stmtCheck = $db->prepare("SELECT id_cuestionario FROM Cuestionario WHERE id_cuestionario=?");
    $stmtCheck->bind_param("i", $id_cuestionario);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    if ($resCheck->num_rows === 0) {
        $stmtCheck->close();
        $db->rollback();
        echo json_encode([
            "status" => "error",
            "message" => "No existe el cuestionario con ID $id_cuestionario"
        ]);
        exit;
    }
    $stmtCheck->close();

    // 2️⃣ Eliminar reglas de calificación
    $stmt = $db->prepare("DELETE FROM Regla_Calificacion WHERE id_cuestionario=?");
    $stmt->bind_param("i", $id_cuestionario);
    $stmt->execute();
    $stmt->close();

    // 3️⃣ Obtener todas las evaluaciones del cuestionario
    $stmtEval = $db->prepare("SELECT id_evaluacion FROM Evaluacion WHERE id_cuestionario=?");
    $stmtEval->bind_param("i", $id_cuestionario);
    $stmtEval->execute();
    $resEval = $stmtEval->get_result();
    $evaluaciones = [];
    while ($row = $resEval->fetch_assoc()) {
        $evaluaciones[] = $row['id_evaluacion'];
    }
    $stmtEval->close();

    foreach ($evaluaciones as $idEval) {
        // 3a️⃣ Eliminar resultados
        $stmtRes = $db->prepare("SELECT id_resultado FROM Resultado WHERE id_evaluacion=?");
        $stmtRes->bind_param("i", $idEval);
        $stmtRes->execute();
        $resRes = $stmtRes->get_result();
        $resultados = [];
        while ($row = $resRes->fetch_assoc()) {
            $resultados[] = $row['id_resultado'];
        }
        $stmtRes->close();

        foreach ($resultados as $idRes) {
            // 3b️⃣ Eliminar evidencia
            $stmtEv = $db->prepare("DELETE FROM Evidencia WHERE id_medida IN (SELECT id_medida FROM Medida WHERE id_resultado=?)");
            $stmtEv->bind_param("i", $idRes);
            $stmtEv->execute();
            $stmtEv->close();

            // 3c️⃣ Eliminar medidas
            $stmtMed = $db->prepare("DELETE FROM Medida WHERE id_resultado=?");
            $stmtMed->bind_param("i", $idRes);
            $stmtMed->execute();
            $stmtMed->close();
        }

        // 3d️⃣ Eliminar resultados
        $stmtDelRes = $db->prepare("DELETE FROM Resultado WHERE id_evaluacion=?");
        $stmtDelRes->bind_param("i", $idEval);
        $stmtDelRes->execute();
        $stmtDelRes->close();

        // 3e️⃣ Eliminar respuestas
        $stmtOpc = $db->prepare("SELECT id_opcion FROM Opcion_Respuesta WHERE id_pregunta IN (SELECT id_pregunta FROM Pregunta WHERE id_cuestionario=?)");
        $stmtOpc->bind_param("i", $id_cuestionario);
        $stmtOpc->execute();
        $resOpc = $stmtOpc->get_result();
        $opciones = [];
        while ($row = $resOpc->fetch_assoc()) {
            $opciones[] = $row['id_opcion'];
        }
        $stmtOpc->close();

        if (count($opciones) > 0) {
            $in = implode(',', array_fill(0, count($opciones), '?'));
            $types = str_repeat('i', count($opciones));
            $stmtResp = $db->prepare("DELETE FROM Respuesta WHERE id_evaluacion=? OR id_opcion_respuesta_select IN ($in)");
            $stmtResp->bind_param($types, ...$opciones);
            $stmtResp->execute();
            $stmtResp->close();
        }

        // 3f️⃣ Eliminar evaluación
        $stmtDelEval = $db->prepare("DELETE FROM Evaluacion WHERE id_evaluacion=?");
        $stmtDelEval->bind_param("i", $idEval);
        $stmtDelEval->execute();
        $stmtDelEval->close();
    }

    // 4️⃣ Eliminar preguntas y opciones
    $stmtPreg = $db->prepare("SELECT id_pregunta FROM Pregunta WHERE id_cuestionario=?");
    $stmtPreg->bind_param("i", $id_cuestionario);
    $stmtPreg->execute();
    $resPreg = $stmtPreg->get_result();
    $preguntas = [];
    while ($row = $resPreg->fetch_assoc()) {
        $preguntas[] = $row['id_pregunta'];
    }
    $stmtPreg->close();

    foreach ($preguntas as $idPregunta) {
        $stmtDelOpc = $db->prepare("DELETE FROM Opcion_Respuesta WHERE id_pregunta=?");
        $stmtDelOpc->bind_param("i", $idPregunta);
        $stmtDelOpc->execute();
        $stmtDelOpc->close();
    }

    $stmtDelPreg = $db->prepare("DELETE FROM Pregunta WHERE id_cuestionario=?");
    $stmtDelPreg->bind_param("i", $id_cuestionario);
    $stmtDelPreg->execute();
    $stmtDelPreg->close();

    // 5️⃣ Finalmente eliminar cuestionario
    $stmtDelCuest = $db->prepare("DELETE FROM Cuestionario WHERE id_cuestionario=?");
    $stmtDelCuest->bind_param("i", $id_cuestionario);
    $stmtDelCuest->execute();
    $stmtDelCuest->close();

    $db->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Cuestionario y todas sus dependencias eliminadas correctamente"
    ]);

} catch (Exception $e) {
    $db->rollback();
    echo json_encode([
        "status" => "error",
        "message" => "Error al eliminar el cuestionario: " . $e->getMessage()
    ]);
}

$db->close();
?>
