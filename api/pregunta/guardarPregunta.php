<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

// Validaciones obligatorias
$campos_obligatorios = ["id_cuestionario", "texto_pregunta", "tipo_calificacion", "orden", "dimension", "dominio", "categoria", "grupo_aplicacion", "condicion"];
foreach ($campos_obligatorios as $campo) {
  if (!isset($obj->$campo) || empty(trim($obj->$campo))) {
    echo json_encode(["status" => "error", "message" => "El campo $campo es obligatorio"]);
    exit;
  }
}

// Valores opcionales
$puntaje_maximo = isset($obj->puntaje_maximo) ? (int)$obj->puntaje_maximo : 4;
$obligatoria = isset($obj->obligatoria) ? (int)$obj->obligatoria : 1;
$id_pregunta_dependeDe = isset($obj->id_pregunta_dependeDe) ? (int)$obj->id_pregunta_dependeDe : null;

// Preparar INSERT
$stmt = $db->prepare("INSERT INTO Pregunta 
    (id_cuestionario, texto_pregunta, tipo_calificacion, orden, puntaje_maximo, obligatoria, dimension, dominio, categoria, grupo_aplicacion, id_pregunta_dependeDe, condicion) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
  "issiiissssis",
  $obj->id_cuestionario,
  $obj->texto_pregunta,
  $obj->tipo_calificacion,
  $obj->orden,
  $puntaje_maximo,
  $obligatoria,
  $obj->dimension,
  $obj->dominio,
  $obj->categoria,
  $obj->grupo_aplicacion,
  $id_pregunta_dependeDe,
  $obj->condicion
);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Pregunta guardada correctamente", "id_pregunta" => $stmt->insert_id]);
} else {
  echo json_encode(["status" => "error", "message" => "Error al guardar la pregunta: " . $stmt->error]);
}

$stmt->close();
