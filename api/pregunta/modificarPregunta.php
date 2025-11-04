<?php
// ruta: api/pregunta/modificarPregunta.php
error_reporting(E_ALL);
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
$input = file_get_contents("php://input");
$data = json_decode($input, true);
if (!$data || !isset($data['id_pregunta'])) {
    echo json_encode(["status"=>"error","message"=>"JSON inválido o id_pregunta faltante"]);
    exit;
}

$id_pregunta = (int)$data['id_pregunta'];
$texto = trim($data['texto_pregunta'] ?? '');
$puntaje = isset($data['puntaje_maximo']) ? (int)$data['puntaje_maximo'] : null;
$orden = isset($data['orden']) ? (int)$data['orden'] : null;
$tipo = $data['tipo_calificacion'] ?? null;

$updates = [];
$params = [];
$types = "";

// construir UPDATE dinámico
if ($texto !== "") { $updates[] = "texto_pregunta=?"; $params[] = $texto; $types .= "s"; }
if ($puntaje !== null) { $updates[] = "puntaje_maximo=?"; $params[] = $puntaje; $types .= "i"; }
if ($orden !== null) { $updates[] = "orden=?"; $params[] = $orden; $types .= "i"; }
if ($tipo !== null) { $updates[] = "tipo_calificacion=?"; $params[] = $tipo; $types .= "s"; }

if (count($updates) == 0) {
    echo json_encode(["status"=>"error","message"=>"Nada para actualizar"]);
    exit;
}

$sql = "UPDATE Pregunta SET ".implode(",", $updates)." WHERE id_pregunta=?";
$params[] = $id_pregunta;
$types .= "i";

$stmt = $db->prepare($sql);
if (!$stmt) { echo json_encode(["status"=>"error","message"=>"Error prepare: ".$db->error]); exit; }

// bind dinámico
$bind_names[] = $types;
for ($i=0;$i<count($params);$i++) {
    $bind_name = 'bind' . $i;
    $$bind_name = $params[$i];
    $bind_names[] = &$$bind_name;
}
call_user_func_array([$stmt,'bind_param'], $bind_names);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Pregunta actualizada"]);
} else {
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
}
$stmt->close();
