<?php
require_once '../conexion.php';
header('Content-Type: application/json; charset=utf-8');

$id_cuestionario = isset($_GET['id_cuestionario']) ? (int)$_GET['id_cuestionario'] : 0;
if (!$id_cuestionario) { echo json_encode(["status"=>"error","message"=>"id_cuestionario faltante"]); exit; }

$stmt = $db->prepare("SELECT id_pregunta, id_cuestionario, texto_pregunta, tipo_calificacion, puntaje_maximo, orden, dimension, dominio, categoria, grupo_aplicacion FROM Pregunta WHERE id_cuestionario=? ORDER BY orden ASC");
$stmt->bind_param("i",$id_cuestionario);
$stmt->execute();
$res = $stmt->get_result();
$preguntas = [];
while ($row = $res->fetch_assoc()) $preguntas[] = $row;
echo json_encode(["status"=>"success","preguntas"=>$preguntas]);
$stmt->close();
