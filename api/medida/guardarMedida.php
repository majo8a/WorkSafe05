<?php 
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input")); 

$stmt = $db->prepare("INSERT INTO Medida (id_resultado, tipo_medida, descripcion, id_usuario_responsable, fecha_limite, estado) 
                      VALUES (?,?,?,?,?,?)");

$stmt->bind_param(
    'ississ', 
    $obj->id_resultado, 
    $obj->tipo_medida, 
    $obj->descripcion, 
    $obj->id_usuario_responsable, 
    $obj->fecha_limite, 
    $obj->estado
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registro exitoso"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
