<?php
// ruta: api/pregunta/eliminarPregunta.php
require_once '../conexion.php';
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['id_usuario'])) {
    // Si no hay sesión, asignar un valor por defecto
    $idUsuario = 1;
} else {
    $idUsuario = $_SESSION['id_usuario'];
}

// Asignar la variable para los triggers
$db->query("SET @id_usuario_responsable = $idUsuario");
$id = isset($_GET['id_pregunta']) ? (int)$_GET['id_pregunta'] : 0;
if (!$id) { echo json_encode(["status"=>"error","message"=>"id_pregunta faltante"]); exit; }
$stmt = $db->prepare("DELETE FROM Pregunta WHERE id_pregunta=?");
$stmt->bind_param("i",$id);
if ($stmt->execute()) {
  echo json_encode(["status"=>"success"]);
} else {
  echo json_encode(["status"=>"error","message"=>$stmt->error]);
}
$stmt->close();
