<?php
require_once '../conexion.php';
header('Content-Type: application/json');

// Datos del POST normal (no JSON)
$id_usuario = $_POST['id_usuario'];
$id_capacitacion = $_POST['id_capacitacion'];
$titulo = "Constancia de Capacitación";
$descripcion = "Constancia emitida para capacitación ID: $id_capacitacion";

// Validar archivo
if (!isset($_FILES['archivo'])) {
    echo json_encode(["success" => false, "error" => "No se recibió archivo"]);
    exit;
}

// Subida de archivo
$archivo = $_FILES['archivo'];
$nombreArchivo = "constancia_" . time() . "_" . basename($archivo['name']);
$ruta = "../uploads/" . $nombreArchivo;

if (!move_uploaded_file($archivo['tmp_name'], $ruta)) {
    echo json_encode(["success" => false, "error" => "Error al subir archivo"]);
    exit;
}

try {
    // Guardar documento
    $stmt = $conn->prepare("
        INSERT INTO Documento (titulo, descripcion, ruta_archivo, fecha_publicacion, id_usuario_publicador, acceso_roles)
        VALUES (?, ?, ?, NOW(), ?, ?)
    ");

    $roles = "1,2,3"; // acceso general
    $id_publicador = $id_usuario; 

    $stmt->bind_param("sssiss", 
        $titulo,
        $descripcion,
        $ruta,
        $id_publicador,
        $roles
    );

    if ($stmt->execute()) {
        $id_documento = $conn->insert_id;

        // Asignar documento al usuario
        $stmt2 = $conn->prepare("
            INSERT INTO Usuario_Documento (id_usuario, id_documento, fecha_asignacion, tipo_acceso, firmado)
            VALUES (?, ?, NOW(), 'lectura', 0)
        ");
        $stmt2->bind_param("ii", $id_usuario, $id_documento);
        $stmt2->execute();

        echo json_encode(["success" => true, "id_documento" => $id_documento]);

    } else {
        echo json_encode(["success" => false, "error" => "No se pudo registrar documento"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
