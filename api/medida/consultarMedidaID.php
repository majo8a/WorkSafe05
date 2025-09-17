<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("SELECT id_medida, id_resultado, tipo_medida, descripcion, id_usuario_responsable, fecha_limite, estado 
                      FROM Medida 
                      WHERE id_medida = ?");
$stmt->bind_param('i', $obj->id_medida);
$stmt->execute();
$stmt->bind_result($id_medida, $id_resultado, $tipo_medida, $descripcion, $id_usuario_responsable, $fecha_limite, $estado);

$arr = array();
if ($stmt->fetch()) {
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
