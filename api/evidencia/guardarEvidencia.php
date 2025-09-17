<?php 
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input")); 

$stmt = $db->prepare("INSERT INTO Evidencia (id_medida, tipo_archivo, ruta_archivo, id_usuario_subidoPor) 
                      VALUES (?,?,?,?)");

$stmt->bind_param(
    'issi', 
    $obj->id_medida, 
    $obj->tipo_archivo, 
    $obj->ruta_archivo, 
    $obj->id_usuario_subidoPor
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registro exitoso"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
