<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once '../conexion.php';
require_once '../../app/fpdf/fpdf.php';

if (session_status() === PHP_SESSION_NONE) session_start();

/* ======================
   CONSULTA GENERAL POR CATEGORÍA
====================== */
$sql = "
SELECT 
    r.categoria,
    SUM(r.puntaje_obtenido) AS puntaje_total,
    r.nivel_riesgo
FROM Resultado r
GROUP BY r.categoria, r.nivel_riesgo
ORDER BY r.categoria
";

$res = $db->query($sql);

/* ======================
   PROCESAR DATOS
====================== */
$categorias = [];

$nivelesPeso = [
    'Nulo' => 0,
    'Bajo' => 1,
    'Medio' => 2,
    'Alto' => 3,
    'Muy alto' => 4
];

while ($r = $res->fetch_assoc()) {

    $cat = $r['categoria'] ?: 'Sin categoría';
    $nivel = $r['nivel_riesgo'] ?: 'Nulo';
    $puntaje = (int)$r['puntaje_total'];

    if (!isset($categorias[$cat])) {
        $categorias[$cat] = [
            'puntaje' => 0,
            'nivel' => 'Nulo'
        ];
    }

    $categorias[$cat]['puntaje'] += $puntaje;

    if (($nivelesPeso[$nivel] ?? 0) > ($nivelesPeso[$categorias[$cat]['nivel']] ?? 0)) {
        $categorias[$cat]['nivel'] = $nivel;
    }
}

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
$pdf->Cell(0,10,utf8_decode('REPORTE GENERAL POR CATEGORÍA - NOM-035'),0,1,'C');

$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0);
$pdf->Cell(0,6,'Fecha de emisión: '.date('d/m/Y H:i'),0,1,'C');
$pdf->Ln(6);

/* DESCRIPCIÓN */
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(0,6,utf8_decode(
"El presente reporte muestra los resultados generales obtenidos por categoría de riesgo psicosocial, conforme a los criterios establecidos en la Norma Oficial Mexicana NOM-035-STPS-2018."
));
$pdf->Ln(5);

/* TABLA CATEGORÍAS */
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,'Resultados por Categoría',0,1);

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(1,22,64);
$pdf->SetTextColor(255);
$pdf->Cell(100,7,'Categoría',1,0,'C',true);
$pdf->Cell(40,7,'Puntaje Total',1,0,'C',true);
$pdf->Cell(0,7,'Nivel de Riesgo',1,1,'C',true);

$pdf->SetFont('Arial','',9);
$pdf->SetTextColor(0);

foreach ($categorias as $cat => $c) {
    $pdf->Cell(100,7,utf8_decode($cat),1);
    $pdf->Cell(40,7,$c['puntaje'],1);
    $pdf->Cell(0,7,$c['nivel'],1,1);
}

$pdf->Ln(6);

/* LEYENDA */
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,7,'Interpretación del Nivel de Riesgo',0,1);

$pdf->SetFont('Arial','',8);
$pdf->MultiCell(0,5,utf8_decode(
"Muy Alto: Requiere análisis profundo y programa de intervención.
Alto: Implementar programa de intervención.
Medio: Revisar y reforzar políticas preventivas.
Bajo: Mantener entorno organizacional favorable.
Nulo: Riesgo despreciable."
));

/* PIE */
$pdf->Ln(5);
$pdf->SetFont('Arial','I',8);
$pdf->Cell(0,6,'WorkSafe | NOM-035-STPS-2018',0,1,'C');

/* DESCARGA DIRECTA */
$pdf->Output('D','Reporte_General_Categorias_NOM035.pdf');
