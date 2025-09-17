<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_cambio, id_usuario_responsable, tipo_objeto, id_objeto, campo, valor_antiguo, valor_nuevo, fecha_cambio 
                      FROM Historial_Cambios 
                      WHERE id_cambio = ?");
$stmt->bind_param('i', $obj->id_cambio);
$stmt->execute();
$stmt->bind_result($id_cambio, $id_usuario_responsable, $tipo_objeto, $id_objeto, $campo, $valor_antiguo, $valor_nuevo, $fecha_cambio);

$arr = array();
if ($stmt->fetch()) {
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
