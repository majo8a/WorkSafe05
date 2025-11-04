<?php
require_once '../conexion.php';
header('Content-Type: application/json; charset=utf-8');
$data = json_decode(file_get_contents("php://input"), true);
session_start();

if (!isset($_SESSION['id_usuario'])) {
    // Si no hay sesión, asignar un valor por defecto
    $idUsuario = 1;
} else {
    $idUsuario = $_SESSION['id_usuario'];
}

// Asignar la variable para los triggers
$db->query("SET @id_usuario_responsable = $idUsuario");

if (
  empty($data['cuestionario']['nombre']) ||
  empty($data['cuestionario']['descripcion']) ||
  empty($data['cuestionario']['version']) ||
  !isset($data['cuestionario']['id_usuario_creador'])
) {
  echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
  exit;
}

$c = $data['cuestionario'];
$estado = !empty($c['estado']) ? $c['estado'] : 'Activo';

$stmt = $db->prepare("INSERT INTO Cuestionario (nombre, descripcion, version, estado, fecha_creacion, id_usuario_creador)
VALUES (?, ?, ?, ?, NOW(), ?)");
$stmt->bind_param("ssssi", $c['nombre'], $c['descripcion'], $c['version'], $estado, $c['id_usuario_creador']);
$stmt->execute();
$id_cuestionario = $stmt->insert_id;
$stmt->close();

// Insertar preguntas
if (!empty($data['preguntas'])&& is_array($data['preguntas'])) {
  $qStmt = $db->prepare("INSERT INTO Pregunta (id_cuestionario, texto_pregunta, tipo_calificacion, puntaje_maximo, orden, dimension, dominio, categoria, grupo_aplicacion, condicion)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '')");
  $oStmt = $db->prepare("INSERT INTO Opcion_Respuesta (id_pregunta, etiqueta, valor) VALUES (?, ?, ?)");
  $orden = 1;

  foreach ($data['preguntas'] as $p) {
    $ordenFinal = isset($p['orden']) ? intval($p['orden']) : $orden++;
    $qStmt->bind_param(
      "ississsss",
      $id_cuestionario,
      $p['texto_pregunta'],
      $p['tipo_calificacion'],
      $p['puntaje_maximo'],
      $ordenFinal,
      $p['dimension'],
      $p['dominio'],
      $p['categoria'],
      $p['grupo_aplicacion']
    );
    $qStmt->execute();
    $id_pregunta = $qStmt->insert_id;

     if (!empty($p['opciones'])) {
            foreach ($p['opciones'] as $o) {
                $etiqueta = $o['etiqueta'] ?? '';
                $valor = $o['valor'] ?? 0;
                $oStmt->bind_param("isi", $id_pregunta, $etiqueta, $valor);
                $oStmt->execute();
            }
        }
  }
  $qStmt->close();
}

echo json_encode(["status" => "success", "message" => "Cuestionario guardado correctamente"]);
?>
