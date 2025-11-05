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
    // =====================================
    // 1️⃣ Verificar si ya respondió este cuestionario
    // =====================================
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

    // =====================================
    // 2️⃣ Crear nueva evaluación
    // =====================================
    $sqlEval = "INSERT INTO Evaluacion (id_usuario, id_cuestionario, fecha_aplicacion, estado)
                VALUES (?, ?, NOW(), 'completado')";
    $stmtEval = $db->prepare($sqlEval);
    $stmtEval->bind_param("ii", $idUsuario, $idCuestionario);
    $stmtEval->execute();
    $idEvaluacion = $stmtEval->insert_id;

    // =====================================
    // 3️⃣ Insertar las respuestas seleccionadas
    // =====================================
    $sqlPreguntas = "SELECT id_pregunta FROM Pregunta WHERE id_cuestionario = ? ORDER BY orden ASC";
    $stmtPreg = $db->prepare($sqlPreguntas);
    $stmtPreg->bind_param("i", $idCuestionario);
    $stmtPreg->execute();
    $resultPreg = $stmtPreg->get_result();
    $preguntas = [];
    while ($row = $resultPreg->fetch_assoc()) {
        $preguntas[] = $row['id_pregunta'];
    }

    $sqlOpciones = "SELECT id_pregunta, id_opcion, valor 
                    FROM Opcion_Respuesta 
                    WHERE id_pregunta IN (SELECT id_pregunta FROM Pregunta WHERE id_cuestionario = ?)";
    $stmtOpciones = $db->prepare($sqlOpciones);
    $stmtOpciones->bind_param("i", $idCuestionario);
    $stmtOpciones->execute();
    $resOpciones = $stmtOpciones->get_result();
    $opcionesPorPregunta = [];
    while ($row = $resOpciones->fetch_assoc()) {
        $opcionesPorPregunta[$row['id_pregunta']][$row['id_opcion']] = $row['valor'];
    }

    $sqlResp = "INSERT INTO Respuesta (id_pregunta, id_evaluacion, id_opcion_respuesta_select, valor, fecha_respuesta)
                VALUES (?, ?, ?, ?, NOW())";
    $stmtResp = $db->prepare($sqlResp);

    foreach ($respuestas as $i => $idOpcion) {
        $idPregunta = $preguntas[$i] ?? null;
        if (!$idPregunta) continue;

        $valorOpcion = $opcionesPorPregunta[$idPregunta][$idOpcion] ?? null;
        if ($valorOpcion === null) continue;

        $stmtResp->bind_param("iiii", $idPregunta, $idEvaluacion, $idOpcion, $valorOpcion);
        $stmtResp->execute();
    }

    // =====================================
    // 4️⃣ Calcular resultados (categoría, dominio y dimensión)
    // =====================================
    $sqlPuntajes = "
        SELECT 
            COALESCE(p.categoria, 'Desconocido') AS categoria,
            COALESCE(p.dominio, 'Desconocido') AS dominio,
            COALESCE(p.dimension, 'Desconocido') AS dimension,
            SUM(r.valor) AS puntaje
        FROM Respuesta r
        INNER JOIN Pregunta p ON p.id_pregunta = r.id_pregunta
        WHERE r.id_evaluacion = ?
        GROUP BY p.categoria, p.dominio, p.dimension
    ";
    $stmtPuntajes = $db->prepare($sqlPuntajes);
    $stmtPuntajes->bind_param("i", $idEvaluacion);
    $stmtPuntajes->execute();
    $resPuntajes = $stmtPuntajes->get_result();

    // =====================================
    // 5️⃣ Insertar en tabla Resultado
    // =====================================
    $sqlResultado = "
        INSERT INTO Resultado (
            id_evaluacion, categoria, dominio, dimension, 
            puntaje_obtenido, nivel_riesgo, interpretacion
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";
    $stmtResultado = $db->prepare($sqlResultado);

    while ($row = $resPuntajes->fetch_assoc()) {
        $categoria = $row['categoria'];
        $dominio = $row['dominio'];
        $dimension = $row['dimension'];
        $puntaje = $row['puntaje'];

        $nivel = determinarNivelRiesgo($categoria, $puntaje);
        $interpretacion = "Nivel de riesgo: " . $nivel;

        $stmtResultado->bind_param("isssiss", $idEvaluacion, $categoria, $dominio, $dimension, $puntaje, $nivel, $interpretacion);
        $stmtResultado->execute();
    }

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

// =====================================
// 🔹 Función de clasificación NOM-035
// =====================================
function determinarNivelRiesgo($categoria, $puntaje) {
    $categoria = trim(mb_strtolower($categoria, 'UTF-8'));

    $rangos = [
        'ambiente de trabajo' => [
            ['Nulo', 0, 5],
            ['Bajo', 5, 9],
            ['Medio', 9, 11],
            ['Alto', 11, 14],
            ['Muy alto', 14, 999]
        ],
        'factores propios de la actividad' => [
            ['Nulo', 0, 15],
            ['Bajo', 15, 30],
            ['Medio', 30, 45],
            ['Alto', 45, 60],
            ['Muy alto', 60, 999]
        ],
        'organización del tiempo de trabajo' => [
            ['Nulo', 0, 5],
            ['Bajo', 5, 7],
            ['Medio', 7, 10],
            ['Alto', 10, 13],
            ['Muy alto', 13, 999]
        ],
        'liderazgo y relaciones en el trabajo' => [
            ['Nulo', 0, 14],
            ['Bajo', 14, 29],
            ['Medio', 29, 42],
            ['Alto', 42, 58],
            ['Muy alto', 58, 999]
        ],
        'entorno organizacional' => [
            ['Nulo', 0, 10],
            ['Bajo', 10, 14],
            ['Medio', 14, 18],
            ['Alto', 18, 23],
            ['Muy alto', 23, 999]
        ]
    ];

    if (!isset($rangos[$categoria])) {
        return "Desconocido";
    }

    foreach ($rangos[$categoria] as [$nivel, $min, $max]) {
        if ($puntaje >= $min && $puntaje < $max) {
            return $nivel;
        }
    }

    return "Desconocido";
}
?>
