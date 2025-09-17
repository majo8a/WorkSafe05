<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

// Validaciones
if (!isset($obj->id_pregunta) || !is_numeric($obj->id_pregunta)) {
  echo json_encode(["status" => "error", "message" => "El ID de la pregunta es obligatorio"]);
  exit;
}

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

// Preparar UPDATE
$stmt = $db->prepare("UPDATE Pregunta SET 
    id_cuestionario=?, texto_pregunta=?, tipo_calificacion=?, orden=?, puntaje_maximo=?, obligatoria=?, dimension=?, dominio=?, categoria=?, grupo_aplicacion=?, id_pregunta_dependeDe=?, condicion=? 
    WHERE id_pregunta=?");

$stmt->bind_param(
  "issiiissssisi",
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
  $obj->condicion,
  $obj->id_pregunta
);

if ($stmt->execute()) {
  if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Pregunta modificada correctamente"]);
  } else {
    echo json_encode(["status" => "warning", "message" => "No se encontraron cambios o la pregunta no existe"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Error al modificar la pregunta: " . $stmt->error]);
}

$stmt->close();
