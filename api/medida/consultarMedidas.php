<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_medida, id_resultado, tipo_medida, descripcion, id_usuario_responsable, fecha_limite, estado FROM Medida");
$stmt->execute();
$stmt->bind_result($id_medida, $id_resultado, $tipo_medida, $descripcion, $id_usuario_responsable, $fecha_limite, $estado);

$arr = array();
while ($stmt->fetch()) {
    $arr[] = array(
        'id_medida' => $id_medida,
        'id_resultado' => $id_resultado,
        'tipo_medida' => $tipo_medida,
        'descripcion' => $descripcion,
        'id_usuario_responsable' => $id_usuario_responsable,
        'fecha_limite' => $fecha_limite,
        'estado' => $estado
    );
}

$stmt->close();
echo json_encode($arr);
?>
