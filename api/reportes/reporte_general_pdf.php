<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once '../conexion.php';
require_once '../../app/fpdf/fpdf.php';

if (session_status() === PHP_SESSION_NONE) session_start();

/* ======================
   FILTROS
====================== */
$categoria = $_GET['categoria'] ?? '';
$dominio   = $_GET['dominio'] ?? '';
$filtroNivel = $_GET['nivel'] ?? '';

$where = "1=1";
$params = [];
$types = "";

if ($categoria)   { $where .= " AND r.categoria=?";    $params[] = $categoria;   $types .= "s"; }
if ($dominio)     { $where .= " AND r.dominio=?";      $params[] = $dominio;     $types .= "s"; }
if ($filtroNivel) { $where .= " AND r.nivel_riesgo=?"; $params[] = $filtroNivel; $types .= "s"; }

/* ======================
   CONSULTA GENERAL
====================== */
$sql = "
SELECT 
    r.categoria,
    r.dominio,
    SUM(r.puntaje_obtenido) AS puntaje_total,
    r.nivel_riesgo
FROM Resultado r
INNER JOIN Evaluacion e ON e.id_evaluacion = r.id_evaluacion
WHERE $where
GROUP BY r.categoria, r.dominio, r.nivel_riesgo
ORDER BY r.categoria, r.dominio
";

$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

/* ======================
   ESTRUCTURAS
====================== */
$categorias = [];
$dominios = [];
$totalEvaluaciones = 0;

$nivelesPeso = [
    'Nulo' => 0,
    'Bajo' => 1,
    'Medio' => 2,
    'Alto' => 3,
    'Muy alto' => 4
];

$nivelGlobal = 'Nulo';

/* ======================
   PROCESAR DATOS
====================== */
while ($r = $res->fetch_assoc()) {

    $cat = $r['categoria'] ?: 'Sin categoría';
    $dom = $r['dominio'] ?: 'Sin dominio';
    $nivel = $r['nivel_riesgo'] ?: 'Nulo';
    $puntaje = (int)$r['puntaje_total'];

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

    // Nivel global
    if (($nivelesPeso[$nivel] ?? 0) > ($nivelesPeso[$nivelGlobal] ?? 0)) {
        $nivelGlobal = $nivel;
    }

    $totalEvaluaciones++;
}

/* ======================
   PDF
====================== */
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();

/* LOGO */
$pdf->Image('../../src/img/logo.png',10,10,35);
$pdf->Ln(20);

/* ENCABEZADO */
$pdf->SetFont('Arial','B',14);
$pdf->SetTextColor(1,22,64);
$pdf->Cell(0,10,utf8_decode('REPORTE GENERAL DE RESULTADOS NOM-035'),0,1,'C');

$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0);
$pdf->Cell(0,6,'Fecha de emisión: '.date('d/m/Y H:i'),0,1,'C');
$pdf->Ln(6);

/* RESUMEN */
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,'Resumen General',0,1);

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,"Total de registros analizados: $totalEvaluaciones",0,1);
$pdf->Cell(0,6,"Nivel de riesgo predominante: $nivelGlobal",0,1);
$pdf->Ln(5);

/* TABLA CATEGORÍAS */
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,'Resultados por Categoria',0,1);

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(1,22,64);
$pdf->SetTextColor(255);
$pdf->Cell(90,7,'Categoria',1,0,'C',true);
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
$pdf->Cell(0,7,'Interpretacion del Nivel de Riesgo',0,1);

$pdf->SetFont('Arial','',8);
$pdf->MultiCell(0,5,utf8_decode(
"Muy Alto: Requiere análisis profundo y programa de intervención.
Alto: Implementar programa de intervención.
Medio: Revisar política de prevención.
Bajo: Difundir políticas de prevención.
Nulo: Riesgo despreciable."
));

/* PIE */
$pdf->Ln(5);
$pdf->SetFont('Arial','I',8);
$pdf->Cell(0,6,'WorkSafe | NOM-035-STPS-2018',0,1,'C');

/* DESCARGA DIRECTA */
$pdf->Output('D','Reporte_General_NOM035.pdf');
