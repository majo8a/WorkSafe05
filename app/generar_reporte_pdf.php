<?php
require_once '../api/conexion.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Forzar codificación UTF-8 en MySQL
mysqli_set_charset($db, "utf8");

// Verificar rol Psicólogo (id_rol = 2)
$idUsuarioSesion = $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
$idRolSesion = $_SESSION['role'] ?? $_SESSION['role'] ?? null;
if (!$idUsuarioSesion) die("⚠️ Debes iniciar sesión.");
if ((int)$idRolSesion !== 2) die("⚠️ Acceso restringido. Solo psicólogos pueden generar reportes.");

// Importar FPDF
require_once 'fpdf/fpdf.php';

// Recibir gráfica (opcional)
$grafica = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = json_decode(file_get_contents('php://input'), true);
    $grafica = $json['grafica'] ?? null;
}

// Obtener id_evaluacion (si viene por GET)
$idEvaluacion = isset($_GET['id_evaluacion']) ? (int)$_GET['id_evaluacion'] : 0;
if (!$idEvaluacion) die("⚠️ Evaluación no especificada.");

// Obtener datos de evaluación
$sqlEval = "
    SELECT e.id_evaluacion, e.fecha_aplicacion, e.estado, 
           u.nombre_completo AS nombre_usuario, c.nombre AS nombre_cuestionario
    FROM Evaluacion e
    INNER JOIN Usuario u ON u.id_usuario = e.id_usuario
    INNER JOIN Cuestionario c ON c.id_cuestionario = e.id_cuestionario
    WHERE e.id_evaluacion = ? LIMIT 1
";
$stmt = $db->prepare($sqlEval);
$stmt->bind_param("i", $idEvaluacion);
$stmt->execute();
$eval = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$eval) die("⚠️ Evaluación no encontrada.");

// Obtener resultados detallados
$sqlRes = "
    SELECT categoria, dominio, dimension, puntaje_obtenido, nivel_riesgo
    FROM Resultado
    WHERE id_evaluacion = ?
    ORDER BY categoria, dominio, dimension
";
$stmtR = $db->prepare($sqlRes);
$stmtR->bind_param("i", $idEvaluacion);
$stmtR->execute();
$res = $stmtR->get_result();

$datos = [];
while ($row = $res->fetch_assoc()) {
    $cat = $row['categoria'] ?: 'Sin categoría';
    if (!isset($datos[$cat])) $datos[$cat] = [];
    $datos[$cat][] = $row;
}
$stmtR->close();

// Recomendación global
function obtenerRecomendacion($nivelGlobal) {
    switch ($nivelGlobal) {
        case 'Muy alto':
            return "Nivel Muy Alto: Requiere un análisis profundo, elaboración de un Programa de Intervención y campañas de sensibilización.";
        case 'Alto':
            return "Nivel Alto: Se debe implementar un Programa de Intervención con evaluaciones específicas y reforzamiento de políticas.";
        case 'Medio':
            return "Nivel Medio: Revisión y reforzamiento de la política de prevención de riesgos psicosociales.";
        case 'Bajo':
            return "Nivel Bajo: Promover entorno organizacional favorable y continuar difusión de políticas.";
        default:
            return "Nivel Nulo o Despreciable: No se requieren medidas adicionales.";
    }
}
$recomendacion = obtenerRecomendacion($eval['estado']);

// Crear PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Encabezado principal
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte de Evaluación NOM-035'), 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode('Cuestionario: ' . $eval['nombre_cuestionario']), 0, 1);
$pdf->Cell(0, 8, utf8_decode('Usuario evaluado: ' . $eval['nombre_usuario']), 0, 1);
$pdf->Cell(0, 8, utf8_decode('Fecha de aplicación: ' . date('d/m/Y H:i', strtotime($eval['fecha_aplicacion']))), 0, 1);
$pdf->Cell(0, 8, utf8_decode('Nivel global: ' . strtoupper($eval['estado'])), 0, 1);
$pdf->Ln(6);

// 🔹 Insertar gráfica si existe
if ($grafica) {
    $grafica = str_replace('data:image/png;base64,', '', $grafica);
    $grafica = str_replace(' ', '+', $grafica);
    $imagen = base64_decode($grafica);
    $rutaImagen = 'grafica_temp.png';
    file_put_contents($rutaImagen, $imagen);

    // Insertar la imagen en el PDF
    $pdf->Image($rutaImagen, 25, $pdf->GetY(), 160, 80);
    $pdf->Ln(90);

    unlink($rutaImagen); // Eliminar temporal
}

// Título tabla
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(0, 8, utf8_decode('Resultados por Categoría'), 0, 1);
$pdf->Ln(2);

// Encabezados de tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(55, 8, utf8_decode('Categoría'), 1, 0, 'C', true);
$pdf->Cell(45, 8, utf8_decode('Dominio'), 1, 0, 'C', true);
$pdf->Cell(45, 8, utf8_decode('Dimensión'), 1, 0, 'C', true);
$pdf->Cell(20, 8, utf8_decode('Puntaje'), 1, 0, 'C', true);
$pdf->Cell(25, 8, utf8_decode('Nivel'), 1, 1, 'C', true);

// Cuerpo de tabla
$pdf->SetFont('Arial', '', 9);

foreach ($datos as $categoria => $items) {
    foreach ($items as $r) {
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $w = [55, 45, 45, 20, 25];
        $h = 7;

        $xInicio = $x;
        $yInicio = $y;

        // Columna Categoría
        $pdf->MultiCell($w[0], $h, utf8_decode($categoria), 1);
        $yFin = $pdf->GetY();
        $pdf->SetXY($xInicio + $w[0], $yInicio);

        // Columna Dominio
        $pdf->MultiCell($w[1], $h, utf8_decode($r['dominio']), 1);
        $yFin = max($yFin, $pdf->GetY());
        $pdf->SetXY($xInicio + $w[0] + $w[1], $yInicio);

        // Columna Dimensión
        $pdf->MultiCell($w[2], $h, utf8_decode($r['dimension']), 1);
        $yFin = max($yFin, $pdf->GetY());
        $pdf->SetXY($xInicio + $w[0] + $w[1] + $w[2], $yInicio);

        // Puntaje y Nivel
        $pdf->Cell($w[3], $h, $r['puntaje_obtenido'], 1, 0, 'C');
        $pdf->Cell($w[4], $h, utf8_decode($r['nivel_riesgo']), 1, 1, 'C');

        $pdf->SetY($yFin);
    }
}
$pdf->Ln(6);

// Recomendacion
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(0, 8, utf8_decode('Recomendación'), 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 7, utf8_decode($recomendacion), 0, 'J');
$pdf->Ln(8);

// Pie de página
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, utf8_decode('Generado por: ' . ($_SESSION['usuario'] ?? 'Psicólogo')), 0, 1, 'L');
$pdf->Cell(0, 5, utf8_decode('Fecha de emisión: ' . date('d/m/Y H:i')), 0, 1, 'L');

// Salida del PDF
$pdf->Output('I', 'Evaluacion_' . $idEvaluacion . '.pdf');
?>
