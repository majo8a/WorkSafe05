<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

if (!isset($obj->id_opcion) || !is_numeric($obj->id_opcion)) {
  echo json_encode(["status" => "error", "message" => "El ID de la opción es obligatorio"]);
  exit;
}

$stmt = $db->prepare("SELECT id_opcion, etiqueta, valor FROM Opcion_Respuesta WHERE id_opcion=?");
$stmt->bind_param("i", $obj->id_opcion);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode($row);
} else {
  echo json_encode(["status" => "error", "message" => "Opción no encontrada"]);
}

$stmt->close();
