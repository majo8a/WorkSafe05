<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
require_once '../conexion.php';

$sql = "
SELECT 
    c.id_confirmacion,
    c.id_usuario,
    u.nombre_completo,

    c.id_capacitacion,
    cap.tema AS tema_capacitacion,
    cap.descripcion AS descripcion_capacitacion,
    cap.fecha_inicio,
    cap.fecha_fin,
    cap.tipo_modalidad,

    c.tipo_confirmacion,
    c.fecha_confirmacion,
    c.ip_registro,
    c.asistio

FROM Confirmacion c
LEFT JOIN Usuario u ON c.id_usuario = u.id_usuario
LEFT JOIN Capacitacion cap ON c.id_capacitacion = cap.id_capacitacion
ORDER BY c.id_confirmacion DESC
";


$stmt = $db->prepare($sql);

if (!$stmt) {
    die("Error SQL: " . $db->error);
}

$stmt->execute();

$stmt->bind_result(
    $id_confirmacion,
    $id_usuario,
    $nombre_completo,

    $id_capacitacion,
    $tema_capacitacion,
    $descripcion_capacitacion,
    $fecha_inicio,
    $fecha_fin,
    $tipo_modalidad,

    $tipo_confirmacion,
    $fecha_confirmacion,
    $ip_registro,
    $asistio
);

$arr = array();

while ($stmt->fetch()) {
    $arr[] = array(
        'id_confirmacion' => $id_confirmacion,
        'id_usuario' => $id_usuario,
        'nombre_completo' => $nombre_completo,

        'id_capacitacion' => $id_capacitacion,
        'tema_capacitacion' => $tema_capacitacion,
        'descripcion_capacitacion' => $descripcion_capacitacion,
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin,
        'tipo_modalidad' => $tipo_modalidad,

        'tipo_confirmacion' => $tipo_confirmacion,
        'fecha_confirmacion' => $fecha_confirmacion,
        'ip_registro' => $ip_registro,
        'asistio' => $asistio
    );
}

$stmt->close();

echo json_encode($arr);
?>
