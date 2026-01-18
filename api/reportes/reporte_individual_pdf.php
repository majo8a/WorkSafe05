<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once '../conexion.php';
require_once '../../app/fpdf/fpdf.php';

if (session_status() === PHP_SESSION_NONE) session_start();

/* ======================
   ID EVALUACIÓN
====================== */
$idEvaluacion = (int)($_GET['id_evaluacion'] ?? 0);
if (!$idEvaluacion) die("Evaluación no válida");

/* ======================
   DATOS GENERALES
====================== */
$stmtInfo = $db->prepare("
    SELECT 
        u.nombre_completo,
        c.nombre AS cuestionario,
        e.fecha_aplicacion
    FROM Evaluacion e
    INNER JOIN Usuario u ON u.id_usuario = e.id_usuario
    INNER JOIN Cuestionario c ON c.id_cuestionario = e.id_cuestionario
    WHERE e.id_evaluacion = ?
");
$stmtInfo->bind_param("i", $idEvaluacion);
$stmtInfo->execute();
$info = $stmtInfo->get_result()->fetch_assoc();
$stmtInfo->close();

if (!$info) die("Evaluación no encontrada");

/* ======================
   CONSULTA RESULTADOS
====================== */
$stmt = $db->prepare("
    SELECT categoria, dominio, puntaje_obtenido, nivel_riesgo
    FROM Resultado
    WHERE id_evaluacion = ?
    ORDER BY categoria, dominio
");
$stmt->bind_param("i", $idEvaluacion);
$stmt->execute();
$res = $stmt->get_result();

/* ======================
   PROCESAR DATOS
====================== */
$categorias = [];
$dominios   = [];

$nivelesPeso = [
    'Nulo' => 0,
    'Bajo' => 1,
    'Medio' => 2,
    'Alto' => 3,
    'Muy alto' => 4
];

$nivelGlobal = 'Nulo';
$puntajeGlobal = 0;

while ($r = $res->fetch_assoc()) {

    $cat = $r['categoria'] ?: 'Sin categoría';
    $dom = $r['dominio'] ?: 'Sin dominio';
    $nivel = $r['nivel_riesgo'] ?: 'Nulo';
    $puntaje = (int)$r['puntaje_obtenido'];

    // Categorías
    if (!isset($categorias[$cat])) {
        $categorias[$cat] = ['puntaje' => 0, 'nivel' => 'Nulo'];
    }
    $categorias[$cat]['puntaje'] += $puntaje;

    if (($nivelesPeso[$nivel] ?? 0) > ($nivelesPeso[$categorias[$cat]['nivel']] ?? 0)) {
        $categorias[$cat]['nivel'] = $nivel;
    }

    // Dominios
    if (!isset($dominios[$dom])) {
        $dominios[$dom] = ['puntaje' => 0, 'nivel' => 'Nulo'];
    }
    $dominios[$dom]['puntaje'] += $puntaje;

    if (($nivelesPeso[$nivel] ?? 0) > ($nivelesPeso[$dominios[$dom]['nivel']] ?? 0)) {
        $dominios[$dom]['nivel'] = $nivel;
    }

    // Global
    if (($nivelesPeso[$nivel] ?? 0) > ($nivelesPeso[$nivelGlobal] ?? 0)) {
        $nivelGlobal = $nivel;
    }

    $puntajeGlobal += $puntaje;
}

$stmt->close();

/* ======================
   PDF
====================== */
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();

/* LOGO */
$pdf->Image('../../src/img/logo.png',10,10,35);
$pdf->Ln(20);

/* TÍTULO */
$pdf->SetFont('Arial','B',14);
$pdf->SetTextColor(1,22,64);
$pdf->Cell(0,10,utf8_decode('REPORTE INDIVIDUAL DE RIESGOS PSICOSOCIALES NOM-035'),0,1,'C');

$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0);
$pdf->Cell(0,6,'Fecha de emisión: '.date('d/m/Y H:i'),0,1,'C');
$pdf->Ln(6);

/* DATOS EVALUADO */
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,'Datos del Evaluado',0,1);

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'Nombre: '.$info['nombre_completo'],0,1);
$pdf->Cell(0,6,'Cuestionario: '.$info['cuestionario'],0,1);
$pdf->Cell(0,6,'Fecha de aplicación: '.date('d/m/Y H:i', strtotime($info['fecha_aplicacion'])),0,1);
$pdf->Ln(5);

/* RESULTADO GLOBAL */
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,'Resultado Global',0,1);

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'Puntaje total: '.$puntajeGlobal,0,1);
$pdf->Cell(0,6,'Nivel de riesgo global: '.$nivelGlobal,0,1);
$pdf->Ln(5);

/* TABLA CATEGORÍAS */
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,'Resultados por Categoría',0,1);

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(1,22,64);
$pdf->SetTextColor(255);
$pdf->Cell(90,7,'Categoría',1,0,'C',true);
$pdf->Cell(40,7,'Puntaje',1,0,'C',true);
$pdf->Cell(0,7,'Nivel',1,1,'C',true);

$pdf->SetFont('Arial','',9);
$pdf->SetTextColor(0);

foreach ($categorias as $cat => $c) {
    $pdf->Cell(90,7,utf8_decode($cat),1);
    $pdf->Cell(40,7,$c['puntaje'],1);
    $pdf->Cell(0,7,$c['nivel'],1,1);
}

$pdf->Ln(5);

/* TABLA DOMINIOS */
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,'Resultados por Dominio',0,1);

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(1,22,64);
$pdf->SetTextColor(255);
$pdf->Cell(90,7,'Dominio',1,0,'C',true);
$pdf->Cell(40,7,'Puntaje',1,0,'C',true);
$pdf->Cell(0,7,'Nivel',1,1,'C',true);

$pdf->SetFont('Arial','',9);
$pdf->SetTextColor(0);

foreach ($dominios as $dom => $d) {
    $pdf->Cell(90,7,utf8_decode($dom),1);
    $pdf->Cell(40,7,$d['puntaje'],1);
    $pdf->Cell(0,7,$d['nivel'],1,1);
}

$pdf->Ln(6);

/* LEYENDA */
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,7,'Interpretación del Nivel de Riesgo',0,1);

$pdf->SetFont('Arial','',8);
$pdf->MultiCell(0,5,utf8_decode(
"Muy Alto: Requiere análisis profundo y programa de intervención.
Alto: Implementar programa de intervención.
Medio: Revisar política de prevención.
Bajo: Mantener entorno organizacional favorable.
Nulo: Riesgo despreciable."
));

/* PIE */
$pdf->Ln(5);
$pdf->SetFont('Arial','I',8);
$pdf->Cell(0,6,'WorkSafe | NOM-035-STPS-2018',0,1,'C');

/* DESCARGA DIRECTA */
$pdf->Output('D','Reporte_Individual_NOM035_'.$idEvaluacion.'.pdf');
