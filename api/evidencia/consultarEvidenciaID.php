<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_evidencia, id_medida, tipo_archivo, ruta_archivo, fecha_carga, id_usuario_subidoPor 
                      FROM Evidencia 
                      WHERE id_evidencia = ?");
$stmt->bind_param('i', $obj->id_evidencia);
$stmt->execute();
$stmt->bind_result($id_evidencia, $id_medida, $tipo_archivo, $ruta_archivo, $fecha_carga, $id_usuario_subidoPor);

$arr = array();
if ($stmt->fetch()) {
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
