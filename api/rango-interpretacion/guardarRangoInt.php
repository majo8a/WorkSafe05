<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

$stmt = $db->prepare("INSERT INTO Rango_Interpretacion (tipo, objeto, rango_inferior, rango_superior, nivel_riesgo, descripcion) 
                      VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
  'ssiiis',
  $obj->tipo,
  $obj->objeto,
  $obj->rango_inferior,
  $obj->rango_superior,
  $obj->nivel_riesgo,
  $obj->descripcion
);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Rango registrado"]);
} else {
  echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$db->close();
