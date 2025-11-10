<?php
require_once '../api/conexion.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Verificar rol Psicólogo (id_rol = 2)
$idUsuarioSesion = $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
$idRolSesion = $_SESSION['role'] ?? $_SESSION['role'] ?? null;
if (!$idUsuarioSesion) die("⚠️ Debes iniciar sesión.");
if ((int)$idRolSesion !== 2) die("⚠️ Acceso restringido. Solo psicólogos pueden generar reportes.");

// Importar FPDF
require_once 'fpdf/fpdf.php';

// Obtener id_evaluacion
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

// Encabezado
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte de Evaluación NOM-035'), 0, 1, 'C');
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode('Cuestionario: ' . $eval['nombre_cuestionario']), 0, 1);
$pdf->Cell(0, 8, utf8_decode('Usuario evaluado: ' . $eval['nombre_usuario']), 0, 1);
$pdf->Cell(0, 8, utf8_decode('Fecha de aplicación: ' . date('d/m/Y H:i', strtotime($eval['fecha_aplicacion']))), 0, 1);
$pdf->Cell(0, 8, utf8_decode('Nivel global: ' . strtoupper($eval['estado'])), 0, 1);
$pdf->Ln(4);

// Resultados por categoría
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(0, 8, utf8_decode('Resultados por Categoría'), 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(60, 8, 'Categoría', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Dominio', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Dimensión', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Puntaje', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Nivel', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
foreach ($datos as $categoria => $items) {
    foreach ($items as $r) {
        $pdf->Cell(60, 7, utf8_decode($categoria), 1);
        $pdf->Cell(40, 7, utf8_decode($r['dominio']), 1);
        $pdf->Cell(40, 7, utf8_decode($r['dimension']), 1);
        $pdf->Cell(25, 7, $r['puntaje_obtenido'], 1, 0, 'C');
        $pdf->Cell(25, 7, utf8_decode($r['nivel_riesgo']), 1, 1, 'C');
    }
}
$pdf->Ln(6);

// Recomendación
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(0, 8, utf8_decode('Recomendación'), 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 7, utf8_decode($recomendacion), 0, 'J');
$pdf->Ln(8);

// Pie de página
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, utf8_decode('Generado por: ' . ($_SESSION['usuario'] ?? 'Psicólogo')), 0, 1, 'L');
$pdf->Cell(0, 5, utf8_decode('Fecha de emisión: ' . date('d/m/Y H:i')), 0, 1, 'L');
$pdf->Output('I', 'Evaluacion_' . $idEvaluacion . '.pdf');
?>
