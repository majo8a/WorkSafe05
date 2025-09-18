<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("UPDATE Rango_Interpretacion 
    SET tipo = ?, 
        objeto = ?, 
        rango_inferior = ?, 
        rango_superior = ?, 
        nivel_riesgo = ?, 
        descripcion = ? 
    WHERE id_rango = ?");
$stmt->bind_param(
  'ssiiisi',
  $obj->tipo,
  $obj->objeto,
  $obj->rango_inferior,
  $obj->rango_superior,
  $obj->nivel_riesgo,
  $obj->descripcion,
  $obj->id_rango
);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Rango modificado"]);
} else {
  echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
