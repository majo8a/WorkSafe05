<?php
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
require_once '../conexion.php';

$obj = json_decode(file_get_contents("php://input"));

if (!isset($obj->nombre_completo) || empty(trim($obj->nombre_completo))) {
    echo json_encode(["status" => "error", "message" => "El nombre completo es obligatorio"]);
    exit;
}

if (!isset($obj->correo) || empty(trim($obj->correo))) {
    echo json_encode(["status" => "error", "message" => "El correo es obligatorio"]);
    exit;
}

if (!isset($obj->password) || empty(trim($obj->password))) {
    echo json_encode(["status" => "error", "message" => "La contraseña es obligatoria"]);
    exit;
}

if (!isset($obj->id_rol) || !is_numeric($obj->id_rol)) {
    echo json_encode(["status" => "error", "message" => "El rol del usuario es obligatorio"]);
    exit;
}

$password_hash = password_hash($obj->password, PASSWORD_BCRYPT);

$telefono = isset($obj->telefono) ? $obj->telefono : null;
$autenticacion_dos_factores = isset($obj->autenticacion_dos_factores)
    ? (int)$obj->autenticacion_dos_factores
    : 0;

$db->begin_transaction();

try {

    $stmt = $db->prepare("
        INSERT INTO Usuario 
        (nombre_completo, correo, telefono, password_hash, autenticacion_dos_factores, id_rol) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssii",
        $obj->nombre_completo,
        $obj->correo,
        $telefono,
        $password_hash,
        $autenticacion_dos_factores,
        $obj->id_rol
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al guardar el usuario: " . $stmt->error);
    }

    $idUsuarioNuevo = $stmt->insert_id;
    $stmt->close();

    $db->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Usuario creado correctamente. Las evaluaciones fueron asignadas automáticamente.",
        "id_usuario" => $idUsuarioNuevo
    ]);
} catch (Exception $e) {

    $db->rollback();

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
