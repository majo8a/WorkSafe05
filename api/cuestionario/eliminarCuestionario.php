<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));
session_start();

if (!isset($_SESSION['id_usuario'])) {
    // Si no hay sesión, asignar un valor por defecto
    $idUsuario = 1;
} else {
    $idUsuario = $_SESSION['id_usuario'];
}

// Asignar la variable para los triggers
$db->query("SET @id_usuario_responsable = $idUsuario");


if (!isset($obj->id_cuestionario) || !is_numeric($obj->id_cuestionario)) {
  echo json_encode(["status" => "error", "message" => "El ID del cuestionario es obligatorio"]);
  exit;
}

$stmt = $db->prepare("DELETE FROM Cuestionario WHERE id_cuestionario=?");
$stmt->bind_param("i", $obj->id_cuestionario);

if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Cuestionario eliminado correctamente"]);
  } else {
    echo json_encode(["status" => "warning", "message" => "No se encontró el cuestionario con el ID proporcionado"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Error al eliminar el cuestionario: " . $stmt->error]);
}

$stmt->close();
