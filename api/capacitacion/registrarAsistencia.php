<?php
require_once '../conexion.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id_usuario']) || !isset($data['id_capacitacion']) || !isset($data['asistio'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

try {
    $tipo = "asistencia";
    $fecha = date("Y-m-d H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $conn->prepare("
        INSERT INTO Confirmacion (id_usuario, id_capacitacion, tipo_confirmacion, fecha_confirmacion, ip_registro, asistio)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iisssi",
        $data['id_usuario'],
        $data['id_capacitacion'],
        $tipo,
        $fecha,
        $ip,
        $data['asistio']
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "No se pudo registrar la asistencia"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
