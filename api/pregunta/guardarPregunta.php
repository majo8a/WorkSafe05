<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../conexion.php';
header('Content-Type: application/json; charset=utf-8');
session_start();

// ======================================================
// SESIÓN Y USUARIO RESPONSABLE
// ======================================================
$idUsuario = $_SESSION['id_usuario'] ?? 1;
$db->query("SET @id_usuario_responsable = $idUsuario");

// ======================================================
// ENTRADA JSON
// ======================================================
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Log de depuración opcional (puedes eliminarlo luego)
file_put_contents('debug_guardar_pregunta.log', print_r($data, true));

if (!$data) {
    echo json_encode(["status" => "error", "message" => "JSON inválido o vacío"]);
    exit;
}

// ======================================================
// VALIDACIONES INICIALES
// ======================================================
$id_cuestionario = isset($data['id_cuestionario']) ? (int)$data['id_cuestionario'] : 0;

if (!$id_cuestionario) {
    echo json_encode(["status" => "error", "message" => "ID de cuestionario no válido"]);
    exit;
}

// ======================================================
// CALCULAR ORDEN AUTOMÁTICO
// ======================================================
function obtenerSiguienteOrden($db, $id_cuestionario) {
    $stmt = $db->prepare("SELECT IFNULL(MAX(orden), 0) + 1 AS nuevo FROM Pregunta WHERE id_cuestionario = ?");
    $stmt->bind_param("i", $id_cuestionario);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    return (int)$row['nuevo'];
}

// ======================================================
// CASO 1: Inserción múltiple (JSON con "preguntas")
// ======================================================
if (!empty($data['preguntas']) && is_array($data['preguntas'])) {

    $qStmt = $db->prepare("INSERT INTO Pregunta (
        id_cuestionario, texto_pregunta, tipo_calificacion, puntaje_maximo, orden,
        dimension, dominio, categoria, grupo_aplicacion, condicion
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $oStmt = $db->prepare("INSERT INTO Opcion_Respuesta (id_pregunta, etiqueta, valor) VALUES (?, ?, ?)");

    foreach ($data['preguntas'] as $p) {
        $orden = isset($p['orden']) && is_numeric($p['orden'])
            ? (int)$p['orden']
            : obtenerSiguienteOrden($db, $id_cuestionario);

        $qStmt->bind_param(
            "ississssss",
            $id_cuestionario,
            $p['texto_pregunta'],
            $p['tipo_calificacion'] ?? 'Likert',
            $p['puntaje_maximo'] ?? 4,
            $orden,
            $p['dimension'] ?? '',
            $p['dominio'] ?? '',
            $p['categoria'] ?? '',
            $p['grupo_aplicacion'] ?? '',
            $p['condicion'] ?? ''
        );
        $qStmt->execute();
        $id_pregunta = $qStmt->insert_id;

        // Inserta opciones si existen
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
    $oStmt->close();

    echo json_encode(["status" => "success", "message" => "Preguntas guardadas correctamente"]);
    exit;
}

// ======================================================
// CASO 2: Inserción individual (JSON con una sola pregunta)
// ======================================================
$texto = trim($data['texto_pregunta'] ?? '');
if ($texto === '') {
    echo json_encode(["status" => "error", "message" => "El texto de la pregunta no puede estar vacío"]);
    exit;
}

$orden = obtenerSiguienteOrden($db, $id_cuestionario);

$tipo = $data['tipo_calificacion'] ?? 'Likert';
$puntaje = isset($data['puntaje_maximo']) ? (int)$data['puntaje_maximo'] : 4;
$dimension = $data['dimension'] ?? '';
$dominio = $data['dominio'] ?? '';
$categoria = $data['categoria'] ?? '';
$grupo = $data['grupo_aplicacion'] ?? '';
$condicion = $data['condicion'] ?? '';

$stmt = $db->prepare("INSERT INTO Pregunta 
    (id_cuestionario, texto_pregunta, tipo_calificacion, puntaje_maximo, orden, dimension, dominio, categoria, grupo_aplicacion, condicion)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "ississssss",
    $id_cuestionario,
    $texto,
    $tipo,
    $puntaje,
    $orden,
    $dimension,
    $dominio,
    $categoria,
    $grupo,
    $condicion
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Pregunta guardada correctamente"]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Error al guardar la pregunta",
        "error" => $stmt->error
    ]);
}

$stmt->close();
