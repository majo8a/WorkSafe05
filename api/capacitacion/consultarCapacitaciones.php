<?php
header('Content-Type: application/json');
require_once '../conexion.php';

session_start();
$idUsuario = $_SESSION['id'] ?? $_SESSION['id_usuario'] ?? null;

if (!$idUsuario) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit;
}

$query = "
    SELECT 
        c.id_capacitacion,
        c.tema,
        c.descripcion,
        c.fecha_inicio,
        c.fecha_fin,
        c.tipo_modalidad,

        CASE 
            WHEN cf.id_confirmacion IS NOT NULL THEN 1
            ELSE 0
        END AS confirmado

    FROM Capacitacion c
    LEFT JOIN Confirmacion cf
        ON cf.id_capacitacion = c.id_capacitacion
        AND cf.id_usuario = ?

    ORDER BY c.fecha_inicio DESC
";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$resultado = $stmt->get_result();
$data = [];

while ($fila = $resultado->fetch_assoc()) {
    $data[] = $fila;
}

echo json_encode($data);

$stmt->close();
$db->close();
