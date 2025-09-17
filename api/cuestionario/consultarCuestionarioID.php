<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

if (!isset($obj->id_cuestionario) || !is_numeric($obj->id_cuestionario)) {
  echo json_encode(["status" => "error", "message" => "El ID del cuestionario es obligatorio"]);
  exit;
}

$stmt = $db->prepare("
    SELECT c.id_cuestionario, c.nombre, c.descripcion, c.version, c.estado, c.fecha_creacion, 
           c.id_usuario_creador, u.nombre_completo as nombre_creador
    FROM Cuestionario c
    LEFT JOIN Usuario u ON c.id_usuario_creador = u.id_usuario
    WHERE c.id_cuestionario=?
");
$stmt->bind_param("i", $obj->id_cuestionario);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode($row);
} else {
  echo json_encode(["status" => "error", "message" => "Cuestionario no encontrado"]);
}

$stmt->close();
