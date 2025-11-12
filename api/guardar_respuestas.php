<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json');

// Validar sesión
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

    // Crear nueva evaluación
    $sqlEval = "INSERT INTO Evaluacion (id_usuario, id_cuestionario, fecha_aplicacion, estado)
                VALUES (?, ?, NOW(), 'completado')";
    $stmtEval = $db->prepare($sqlEval);
    $stmtEval->bind_param("ii", $idUsuario, $idCuestionario);
    $stmtEval->execute();
    $idEvaluacion = $stmtEval->insert_id;

    //  Obtener preguntas y opciones
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

    // Insertar respuestas
    $sqlResp = "INSERT INTO Respuesta (id_pregunta, id_evaluacion, id_opcion_respuesta_select, valor, fecha_respuesta)
                VALUES (?, ?, ?, ?, NOW())";
    $stmtResp = $db->prepare($sqlResp);

    foreach ($respuestas as $i => $idOpcion) {
        $idPregunta = $preguntas[$i] ?? null;
        if (!$idPregunta) continue;

        $valorOpcion = $opcionesPorPregunta[$idPregunta][$idOpcion] ?? null;
        if ($valorOpcion === null) continue;

        // ✅ Inversión según Tabla 5
        $valorCorregido = obtenerValorInvertido($idPregunta, $valorOpcion);

        $stmtResp->bind_param("iiii", $idPregunta, $idEvaluacion, $idOpcion, $valorCorregido);
        $stmtResp->execute();
    }

    // Calcular resultados agrupados
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

    // Insertar resultados y niveles
    $sqlResultado = "
        INSERT INTO Resultado (
            id_evaluacion, categoria, dominio, dimension, 
            puntaje_obtenido, nivel_riesgo, interpretacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ";
    $stmtResultado = $db->prepare($sqlResultado);

    $puntajeTotal = 0;
    while ($row = $resPuntajes->fetch_assoc()) {
        $categoria = $row['categoria'];
        $dominio = $row['dominio'];
        $dimension = $row['dimension'];
        $puntaje = (int)$row['puntaje'];
        $puntajeTotal += $puntaje;

        $nivel = determinarNivelRiesgo($categoria, $puntaje);
        if ($nivel === 'Desconocido') {
            $nivel = determinarNivelRiesgo($dominio, $puntaje);
        }

        $interpretacion = "Nivel de riesgo: " . $nivel;

        $stmtResultado->bind_param("isssiss", $idEvaluacion, $categoria, $dominio, $dimension, $puntaje, $nivel, $interpretacion);
        $stmtResultado->execute();
    }

    // Nivel global
    $nivelGlobal = determinarNivelRiesgo('global', $puntajeTotal);
    $interpretacionGlobal = "Nivel global: " . $nivelGlobal;

    $sqlGlobal = "INSERT INTO Resultado (id_evaluacion, categoria, puntaje_obtenido, nivel_riesgo, interpretacion)
                  VALUES (?, 'GLOBAL', ?, ?, ?)";
    $stmtGlobal = $db->prepare($sqlGlobal);
    $stmtGlobal->bind_param("iiss", $idEvaluacion, $puntajeTotal, $nivelGlobal, $interpretacionGlobal);
    $stmtGlobal->execute();

    echo json_encode(["success" => true, "nivel_global" => $nivelGlobal, "puntaje_total" => $puntajeTotal]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}



// inversión de valores
function obtenerValorInvertido($idPregunta, $valorOriginal)
{
    $invertidos = [
        2,
        3,
        5,
        6,
        7,
        8,
        9,
        10,
        11,
        12,
        13,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        29,
        54,
        58,
        59,
        60,
        61,
        62,
        63,
        64,
        65,
        66,
        67,
        68,
        69,
        70,
        71,
        72
    ];
    return in_array($idPregunta, $invertidos) ? 4 - $valorOriginal : $valorOriginal;
}

// Clasificación de riesgo
function determinarNivelRiesgo($tipo, $puntaje)
{
    $tipo = mb_strtolower(trim($tipo), 'UTF-8');
    $tipo = str_replace(
        ['á', 'é', 'í', 'ó', 'ú'],
        ['a', 'e', 'i', 'o', 'u'],
        $tipo
    );

    // Normalizar nombres para que coincidan
    $sinonimos = [
        'capacitacion' => 'falta de control sobre el trabajo',
        'definicion de responsabilidades' => 'falta de control sobre el trabajo',
        'escasa claridad de funciones' => 'liderazgo',
        'relaciones sociales en el trabajo' => 'relaciones en el trabajo',
        'cambios no previstos en el trabajo' => 'falta de control sobre el trabajo',
        'falta de control sobre el trabajo' => 'falta de control sobre el trabajo',
        'posibilidades de desarrollo' => 'falta de control sobre el trabajo',
        'falta de reconocimiento y recompensas' => 'insuficiente sentido de pertenencia e inestabilidad',
        'inestabilidad laboral' => 'insuficiente sentido de pertenencia e inestabilidad',
        'reconocimiento del desempeno' => 'reconocimiento del desempeno'
    ];

    if (isset($sinonimos[$tipo])) {
        $tipo = $sinonimos[$tipo];
    }

    // RANGOS CATEGORÍA
    $rangosCategoria = [
        'ambiente de trabajo' => [
            ['Nulo', 0, 5],
            ['Bajo', 5, 9],
            ['Medio', 9, 11],
            ['Alto', 11, 14],
            ['Muy alto', 14, PHP_INT_MAX]
        ],
        'factores propios de la actividad' => [
            ['Nulo', 0, 15],
            ['Bajo', 15, 30],
            ['Medio', 30, 45],
            ['Alto', 45, 60],
            ['Muy alto', 60, PHP_INT_MAX]
        ],
        'organizacion del tiempo de trabajo' => [
            ['Nulo', 0, 5],
            ['Bajo', 5, 7],
            ['Medio', 7, 10],
            ['Alto', 10, 13],
            ['Muy alto', 13, PHP_INT_MAX]
        ],
        'liderazgo y relaciones en el trabajo' => [
            ['Nulo', 0, 14],
            ['Bajo', 14, 29],
            ['Medio', 29, 42],
            ['Alto', 42, 58],
            ['Muy alto', 58, PHP_INT_MAX]
        ],
        'entorno organizacional' => [
            ['Nulo', 0, 10],
            ['Bajo', 10, 14],
            ['Medio', 14, 18],
            ['Alto', 18, 23],
            ['Muy alto', 23, PHP_INT_MAX]
        ],
        'global' => [
            ['Nulo', 0, 50],
            ['Bajo', 50, 75],
            ['Medio', 75, 99],
            ['Alto', 99, 140],
            ['Muy alto', 140, PHP_INT_MAX]
        ]
    ];

    // RANGOS DOMINio
    $rangosDominio = [
        'condiciones en el ambiente de trabajo' => [
            ['Nulo', 0, 5],
            ['Bajo', 5, 9],
            ['Medio', 9, 11],
            ['Alto', 11, 14],
            ['Muy alto', 14, PHP_INT_MAX]
        ],
        'carga de trabajo' => [
            ['Nulo', 0, 15],
            ['Bajo', 15, 21],
            ['Medio', 21, 27],
            ['Alto', 27, 37],
            ['Muy alto', 37, PHP_INT_MAX]
        ],
        'falta de control sobre el trabajo' => [
            ['Nulo', 0, 11],
            ['Bajo', 11, 16],
            ['Medio', 16, 21],
            ['Alto', 21, 25],
            ['Muy alto', 25, PHP_INT_MAX]
        ],
        'jornada de trabajo' => [
            ['Nulo', 0, 1],
            ['Bajo', 1, 2],
            ['Medio', 2, 4],
            ['Alto', 4, 6],
            ['Muy alto', 6, PHP_INT_MAX]
        ],
        'interferencia en la relacion trabajo-familia' => [
            ['Nulo', 0, 4],
            ['Bajo', 4, 6],
            ['Medio', 6, 8],
            ['Alto', 8, 10],
            ['Muy alto', 10, PHP_INT_MAX]
        ],
        'liderazgo' => [
            ['Nulo', 0, 9],
            ['Bajo', 9, 12],
            ['Medio', 12, 16],
            ['Alto', 16, 20],
            ['Muy alto', 20, PHP_INT_MAX]
        ],
        'relaciones en el trabajo' => [
            ['Nulo', 0, 10],
            ['Bajo', 10, 13],
            ['Medio', 13, 17],
            ['Alto', 17, 21],
            ['Muy alto', 21, PHP_INT_MAX]
        ],
        'violencia' => [
            ['Nulo', 0, 7],
            ['Bajo', 7, 10],
            ['Medio', 10, 13],
            ['Alto', 13, 16],
            ['Muy alto', 16, PHP_INT_MAX]
        ],
        'reconocimiento del desempeno' => [
            ['Nulo', 0, 6],
            ['Bajo', 6, 10],
            ['Medio', 10, 14],
            ['Alto', 14, 18],
            ['Muy alto', 18, PHP_INT_MAX]
        ],
        'insuficiente sentido de pertenencia e inestabilidad' => [
            ['Nulo', 0, 4],
            ['Bajo', 4, 6],
            ['Medio', 6, 8],
            ['Alto', 8, 10],
            ['Muy alto', 10, PHP_INT_MAX]
        ]
    ];

    foreach ([$rangosCategoria, $rangosDominio] as $grupo) {
        foreach ($grupo as $clave => $niveles) {
            if (strpos($tipo, $clave) !== false) {
                foreach ($niveles as [$nivel, $min, $max]) {
                    if ($puntaje >= $min && $puntaje < $max) return $nivel;
                }
            }
        }
    }

    return 'Desconocido';
}
