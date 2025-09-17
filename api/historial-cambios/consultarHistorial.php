<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$stmt = $db->prepare("SELECT id_cambio, id_usuario_responsable, tipo_objeto, id_objeto, campo, valor_antiguo, valor_nuevo, fecha_cambio FROM Historial_Cambios");
$stmt->execute();
$stmt->bind_result($id_cambio, $id_usuario_responsable, $tipo_objeto, $id_objeto, $campo, $valor_antiguo, $valor_nuevo, $fecha_cambio);

$arr = array();
while ($stmt->fetch()) {
    $arr[] = array(
        'id_cambio' => $id_cambio,
        'id_usuario_responsable' => $id_usuario_responsable,
        'tipo_objeto' => $tipo_objeto,
        'id_objeto' => $id_objeto,
        'campo' => $campo,
        'valor_antiguo' => $valor_antiguo,
        'valor_nuevo' => $valor_nuevo,
        'fecha_cambio' => $fecha_cambio
    );
}

$stmt->close();
echo json_encode($arr);
?>
