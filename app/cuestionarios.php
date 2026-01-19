<?php
require_once 'encabezado.php';
require_once '../api/conexion.php';

// ============================
// 0️⃣ SESIÓN Y USUARIO
// ============================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$idUsuario = $_SESSION['id'] ?? $_SESSION['id_usuario'] ?? null;

if ($idUsuario === null) {
    die("⚠️ Debes iniciar sesión para responder este cuestionario.");
}

// ============================
// 1️⃣ Obtener el id del cuestionario de la URL
// ============================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("⚠️ Cuestionario no especificado o ID inválido.");
}
$idCuestionario = intval($_GET['id']);

// ============================
// 2️⃣ Obtener el nombre del cuestionario
// ============================
$sqlCuestionario = "SELECT nombre FROM Cuestionario WHERE id_cuestionario = ?";
$stmtC = $db->prepare($sqlCuestionario);
if (!$stmtC) {
    die('Error al preparar la consulta del cuestionario: ' . $db->error);
}
$stmtC->bind_param('i', $idCuestionario);
$stmtC->execute();
$resultC = $stmtC->get_result();

if ($rowC = $resultC->fetch_assoc()) {
    $nombreCuestionario = $rowC['nombre'];
} else {
    die("❌ Cuestionario no encontrado.");
}

// ============================
// 3️⃣ Obtener preguntas y opciones
// ============================
$sql = "
    SELECT 
        p.id_pregunta, p.texto_pregunta, p.orden,
        o.id_opcion, o.etiqueta, o.valor
    FROM Pregunta p
    LEFT JOIN Opcion_Respuesta o ON p.id_pregunta = o.id_pregunta
    WHERE p.id_cuestionario = ?
    ORDER BY p.orden ASC, o.valor ASC
";

$stmt = $db->prepare($sql);
if (!$stmt) {
    die('Error al preparar la consulta: ' . $db->error);
}
$stmt->bind_param('i', $idCuestionario);
$stmt->execute();
$result = $stmt->get_result();

$preguntasAgrupadas = [];

while ($row = $result->fetch_assoc()) {
    $id = $row['id_pregunta'];

    if (!isset($preguntasAgrupadas[$id])) {
        $preguntasAgrupadas[$id] = [
            'id_pregunta' => $id,
            'pregunta' => $row['texto_pregunta'],
            'opciones' => []
        ];
    }

    $preguntasAgrupadas[$id]['opciones'][] = [
        'id_opcion' => $row['id_opcion'],
        'etiqueta' => $row['etiqueta'],
        'valor' => $row['valor']
    ];
}

$preguntas = array_values($preguntasAgrupadas);
$preguntasJson = json_encode($preguntas, JSON_UNESCAPED_UNICODE);
?>

<body>
    <a href="lista_cuestionarios.php" class="btn-volver btn btn-sm btn-secondary">Volver</a>
    <div class="contenedor-cuestionario">
        <div class="info-cuestionario">
            <p class="contador-preguntas">
                Pregunta <span id="numero-pregunta"></span> de <span id="total-preguntas"></span>
            </p>

            <h1 class="titulo-cuestionario">
                <?php echo htmlspecialchars($nombreCuestionario, ENT_QUOTES, 'UTF-8'); ?>
            </h1>

            <p class="pregunta" id="pregunta"></p>
            <div class="opciones" id="opciones"></div>

            <div class="botones-navegacion">
                <button id="boton-anterior" onclick="mostrarAnteriorPregunta()" style="display:none;">
                    Anterior
                </button>
                <button id="boton-siguiente" onclick="mostrarSiguientePregunta()">
                    Siguiente
                </button>
                <button id="boton-finalizar" onclick="finalizarCuestionario()" style="display:none;">
                    Finalizar
                </button>
            </div>
        </div>
    </div>

    <!-- VARIABLES GLOBALES PARA JS -->
    <script>
        const idCuestionario = <?php echo $idCuestionario; ?>;
        const idUsuario = <?php echo json_encode($idUsuario); ?>;
        const preguntas = <?php echo $preguntasJson; ?>;

        console.log("Cuestionario:", idCuestionario);
        console.log("Usuario:", idUsuario);
        console.log("Preguntas:", preguntas);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="controlador/cuestionarios.js"></script>
    <style>
        .btn-volver {
            padding: 3px 8px;
            font-size: 12px;
        }
    </style>
</body>

</html>