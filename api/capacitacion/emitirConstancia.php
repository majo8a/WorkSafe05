<?php
require_once '../conexion.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$id_cap = $data['id_capacitacion'];

$sql = "SELECT 
            c.id_confirmacion AS id,
            c.id_usuario,
            u.nombre_completo,
            c.asistio
        FROM Confirmacion c
        INNER JOIN Usuario u ON u.id_usuario = c.id_usuario
        WHERE c.id_capacitacion = ?
          AND c.tipo_confirmacion = 'asistencia'
          AND c.asistio = 1
        ORDER BY c.id_confirmacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cap);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
