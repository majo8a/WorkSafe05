<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
  echo json_encode(["success" => false, "message" => "Sesión no válida"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$idUsuario = $_SESSION['id'];
$idCuestionario = $data['idCuestionario'] ?? null;

if (!$idCuestionario) {
  echo json_encode(["success" => false, "message" => "Cuestionario requerido"]);
  exit;
}

try {

  // ¿Existe evaluación?
  $sql = "
        SELECT id_evaluacion 
        FROM Evaluacion
        WHERE id_usuario = ?
          AND id_cuestionario = ?
        ORDER BY fecha_aplicacion DESC
        LIMIT 1
    ";
  $stmt = $db->prepare($sql);
  $stmt->bind_param("ii", $idUsuario, $idCuestionario);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($row = $res->fetch_assoc()) {
    // 👉 Ya existe → asegurar pendiente
    $idEvaluacion = $row['id_evaluacion'];

    $upd = $db->prepare("
            UPDATE Evaluacion
            SET estado = 'pendiente'
            WHERE id_evaluacion = ?
        ");
    $upd->bind_param("i", $idEvaluacion);
    $upd->execute();
  } else {
    // 👉 No existe → crearla
    $ins = $db->prepare("
            INSERT INTO Evaluacion
            (id_usuario, id_cuestionario, estado, fecha_aplicacion)
            VALUES (?, ?, 'pendiente', NOW())
        ");
    $ins->bind_param("ii", $idUsuario, $idCuestionario);
    $ins->execute();
    $idEvaluacion = $ins->insert_id;
  }

  echo json_encode([
    "success" => true,
    "idEvaluacion" => $idEvaluacion
  ]);
} catch (Exception $e) {
  echo json_encode([
    "success" => false,
    "message" => $e->getMessage()
  ]);
}
