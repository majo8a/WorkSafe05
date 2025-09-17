<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_evidencia, id_medida, tipo_archivo, ruta_archivo, fecha_carga, id_usuario_subidoPor FROM Evidencia");
$stmt->execute();
$stmt->bind_result($id_evidencia, $id_medida, $tipo_archivo, $ruta_archivo, $fecha_carga, $id_usuario_subidoPor);

$arr = array();
while ($stmt->fetch()) {
    $arr[] = array(
        'id_evidencia' => $id_evidencia,
        'id_medida' => $id_medida,
        'tipo_archivo' => $tipo_archivo,
        'ruta_archivo' => $ruta_archivo,
        'fecha_carga' => $fecha_carga,
        'id_usuario_subidoPor' => $id_usuario_subidoPor
    );
}

$stmt->close();
echo json_encode($arr);
?>
