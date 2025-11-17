<?php
require_once '../conexion.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"));
$id_capacitacion = $data->id_capacitacion ?? null;

if (!$id_capacitacion) {
    echo json_encode([]);
    exit;
}

/*
    Trae solo los usuarios que tengan constancia = 1
    Usando la tabla Confirmacion
*/

$sql = "SELECT u.id_usuario, u.nombre_completo
        FROM Confirmacion c
        INNER JOIN Usuario u ON u.id_usuario = c.id_usuario
        WHERE c.id_capacitacion = ? AND c.constancia = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_capacitacion);
$stmt->execute();
$res = $stmt->get_result();

$lista = [];
while ($row = $res->fetch_assoc()) {
    $lista[] = $row;
}

echo json_encode($lista);
