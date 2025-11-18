<?php
error_reporting(E_ALL);
header('Content-Type: application/json');
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

if (!$obj || !isset($obj->id_usuario) || !isset($obj->id_capacitacion)) {
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];
$fecha = $obj->fecha_confirmacion ?? date("Y-m-d H:i:s");

$stmt = $db->prepare("
    INSERT INTO Confirmacion 
    (id_usuario, id_capacitacion, tipo_confirmacion, fecha_confirmacion, ip_registro, asistio)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    'iisssi',
    $obj->id_usuario,
    $obj->id_capacitacion,
    $obj->tipo_confirmacion,
    $fecha,
    $ip,
    $obj->asistio
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Confirmación registrada"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
?>
