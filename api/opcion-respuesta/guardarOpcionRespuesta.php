<?php
error_reporting(E_ALL);
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

// Validaciones básicas
if (!isset($obj->id_pregunta) || !is_numeric($obj->id_pregunta)) {
  echo json_encode(["status" => "error", "message" => "id_pregunta es obligatorio"]);
  exit;
}
if (!isset($obj->opciones) || !is_array($obj->opciones)) {
  echo json_encode(["status" => "error", "message" => "No hay opciones para guardar"]);
  exit;
}

$stmt = $db->prepare("INSERT INTO Opcion_Respuesta (id_pregunta, etiqueta, valor) VALUES (?, ?, ?)");

foreach ($obj->opciones as $opt) {
    if (!isset($opt->etiqueta) || !isset($opt->valor)) continue;
    $etiqueta = trim($opt->etiqueta);
    $valor = (int)$opt->valor;
    $stmt->bind_param("isi", $obj->id_pregunta, $etiqueta, $valor);
    $stmt->execute();
}

echo json_encode(["status" => "success", "message" => "Opciones guardadas correctamente"]);

$stmt->close();

