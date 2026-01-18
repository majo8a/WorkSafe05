<?php
require_once '../conexion.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$idEvaluacion = (int)($_GET['id_evaluacion'] ?? 0);
if (!$idEvaluacion) die("Evaluación no válida");

/* ===========================
   DATOS GENERALES
=========================== */
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

/* ===========================
   ENCABEZADOS EXCEL
=========================== */
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Evaluacion_$idEvaluacion.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo '<html><head><meta charset="UTF-8"></head><body>';
echo '<table border="1" cellpadding="6" cellspacing="0" width="100%">';

/* ===========================
   TÍTULO CON LOGO
=========================== */
echo '
<tr>
    <td colspan="5" style="
        background-image: url(\'http://localhost/WorkSafe05/src/img/logo.png\');
        background-repeat: no-repeat;
        background-position: left center;
        background-size: 120px;
        padding-left: 140px;
        height: 80px;
        text-align: center;
        vertical-align: middle;
        font-size: 20px;
        font-weight: bold;
        background-color: #011640;
        color: #ffffff;
    ">
        REPORTE INDIVIDUAL DE RIESGOS PSICOSOCIALES NOM-035
    </td>
</tr>
';

/* ===========================
   DATOS DEL EVALUADO
=========================== */
echo '
<tr>
    <td colspan="5" style="background:#f2f2f2;font-weight:bold;">Datos del Evaluado</td>
</tr>
<tr>
    <td colspan="2"><strong>Nombre:</strong></td>
    <td colspan="3">'.$info['nombre_completo'].'</td>
</tr>
<tr>
    <td colspan="2"><strong>Cuestionario:</strong></td>
    <td colspan="3">'.$info['cuestionario'].'</td>
</tr>
<tr>
    <td colspan="2"><strong>Fecha de aplicación:</strong></td>
    <td colspan="3">'.date('d/m/Y H:i', strtotime($info['fecha_aplicacion'])).'</td>
</tr>
<tr><td colspan="5">&nbsp;</td></tr>
';

/* ===========================
   ENCABEZADO TABLA
=========================== */
echo '
<tr style="background:#011640;color:#ffffff;font-weight:bold;text-align:center;">
    <td>Categoría</td>
    <td>Dominio</td>
    <td>Dimensión</td>
    <td>Puntaje</td>
    <td>Nivel</td>
</tr>
';

/* ===========================
   RESULTADOS
=========================== */
$stmt = $db->prepare("
    SELECT categoria, dominio, dimension, puntaje_obtenido, nivel_riesgo
    FROM Resultado
    WHERE id_evaluacion = ?
    ORDER BY categoria, dominio, dimension
");
$stmt->bind_param("i", $idEvaluacion);
$stmt->execute();
$res = $stmt->get_result();

while ($r = $res->fetch_assoc()) {

    $colorNivel = match ($r['nivel_riesgo']) {
        'Muy alto' => '#f8d7da',
        'Alto'     => '#fff3cd',
        'Medio'    => '#cff4fc',
        'Bajo'     => '#d1e7dd',
        default    => '#eeeeee'
    };

    echo '
    <tr>
        <td>'.$r['categoria'].'</td>
        <td>'.$r['dominio'].'</td>
        <td>'.$r['dimension'].'</td>
        <td align="center">'.$r['puntaje_obtenido'].'</td>
        <td style="background:'.$colorNivel.';font-weight:bold;text-align:center;">
            '.$r['nivel_riesgo'].'
        </td>
    </tr>
    ';
}

$stmt->close();

/* ===========================
   LEYENDA
=========================== */
echo '
<tr><td colspan="5">&nbsp;</td></tr>
<tr>
    <td colspan="5" style="font-weight:bold;">Interpretación de niveles</td>
</tr>
<tr>
    <td colspan="5">
        MUY ALTO: Requiere análisis profundo y programa de intervención.<br>
        ALTO: Implementar programa de intervención.<br>
        MEDIO: Revisar política de prevención.<br>
        BAJO: Mantener entorno favorable.<br>
        NULO: Riesgo despreciable.
    </td>
</tr>
';

/* ===========================
   PIE
=========================== */
echo '
<tr><td colspan="5">&nbsp;</td></tr>
<tr>
    <td colspan="5" align="center" style="font-size:10px;">
        Smart-035 | Reporte generado el '.date('d/m/Y H:i').'
    </td>
</tr>
';

echo '</table></body></html>';
