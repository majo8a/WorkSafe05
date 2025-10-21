<?php
require_once 'encabezado.php';
require_once '../api/conexion.php';

$idCuestionario = 1;

// ============================
// Obtener el nombre del cuestionario
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
    $nombreCuestionario = "Cuestionario"; // fallback por si no encuentra
}

// ============================
// Obtener preguntas y opciones
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

// Reindexar el array para convertirlo en una lista secuencial
$preguntas = array_values($preguntasAgrupadas);
$preguntasJson = json_encode($preguntas, JSON_UNESCAPED_UNICODE);
?>

<body>
    <div class="contenedor-cuestionario">
        <div class="info-cuestionario">
            <p class="contador-preguntas">Pregunta <span id="numero-pregunta"></span> de <span id="total-preguntas"></span></p>
            <h1 class="titulo-cuestionario"><?php echo htmlspecialchars($nombreCuestionario, ENT_QUOTES, 'UTF-8'); ?></h1>

            <p class="pregunta" id="pregunta"></p>
            <div class="opciones" id="opciones"></div>
            <div class="botones-navegacion">
                <button id="boton-anterior" onclick="mostrarAnteriorPregunta()" style="display:none;">Anterior</button>
                <button id="boton-siguiente" onclick="mostrarSiguientePregunta()">Siguiente</button>
                <button id="boton-finalizar" onclick="finalizarCuestionario()" style="display:none;">Finalizar</button>
            </div>
        </div>
    </div>
    <script>
        const preguntas = <?php echo $preguntasJson; ?>;
    </script>
    <script src="controlador/cuestionarios.js"></script>
</body>

</html>