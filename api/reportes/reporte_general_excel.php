<?php
require_once '../conexion.php';

if (session_status() === PHP_SESSION_NONE) session_start();

/* ===============================
   HEADERS PARA EXCEL
================================ */
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=Reporte_General_NOM035.xls");
header("Pragma: no-cache");
header("Expires: 0");

/* ===============================
   CONSULTA GENERAL
================================ */
$sql = "
SELECT 
    r.categoria,
    r.dominio,
    r.dimension,
    SUM(r.puntaje_obtenido) AS puntaje_total,
    r.nivel_riesgo
FROM Resultado r
INNER JOIN Evaluacion e ON e.id_evaluacion = r.id_evaluacion
GROUP BY r.categoria, r.dominio, r.dimension, r.nivel_riesgo
ORDER BY r.categoria, r.dominio
";

$res = $db->query($sql);

/* ===============================
   HTML QUE EXCEL INTERPRETA
================================ */
echo '
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial; }
    .titulo {
        background-color: #336FF4;
        color: #ffffff;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        padding: 10px;
    }
    .subtitulo {
        background-color: #F2F2F2;
        font-weight: bold;
        padding: 5px;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th {
        background-color: #1F4ED8;
        color: #ffffff;
        border: 1px solid #000;
        padding: 6px;
    }
    td {
        border: 1px solid #000;
        padding: 6px;
    }
    .alto { background-color: #F2994A; }
    .muyalto { background-color: #EB5757; color: #fff; }
    .medio { background-color: #F2C94C; }
    .bajo { background-color: #6FCF97; }
    .nulo { background-color: #E0E0E0; }
</style>
</head>
<body>
<table>
<tr>
    <td colspan="5" class="titulo">
        REPORTE GENERAL DE RIESGOS PSICOSOCIALES NOM-035
    </td>
</tr>
<tr>
    <td colspan="5" class="subtitulo">
        Fecha de generación: '.date('d/m/Y H:i').'
    </td>
</tr>
</table>

<br>

<table>
<thead>
<tr>
    <th>Categoría</th>
    <th>Dominio</th>
    <th>Dimensión</th>
    <th>Puntaje</th>
    <th>Nivel de Riesgo</th>
</tr>
</thead>
<tbody>
';

while ($r = $res->fetch_assoc()) {

    $nivel = strtolower($r['nivel_riesgo'] ?? 'nulo');

    $clase = match ($nivel) {
        'muy alto' => 'muyalto',
        'alto'     => 'alto',
        'medio'    => 'medio',
        'bajo'     => 'bajo',
        default    => 'nulo'
    };

    echo "
    <tr class='{$clase}'>
        <td>{$r['categoria']}</td>
        <td>{$r['dominio']}</td>
        <td>{$r['dimension']}</td>
        <td>{$r['puntaje_total']}</td>
        <td>{$r['nivel_riesgo']}</td>
    </tr>
    ";
}

echo '
</tbody>
</table>

<br>

<table>
<tr>
    <td class="subtitulo">Interpretación</td>
</tr>
<tr>
    <td>
        MUY ALTO: Requiere análisis profundo y programa de intervención.<br>
        ALTO: Implementar programa de intervención.<br>
        MEDIO: Revisar política de prevención.<br>
        BAJO: Mantener políticas de prevención.<br>
        NULO: Riesgo despreciable.
    </td>
</tr>
</table>

</body>
</html>
';
