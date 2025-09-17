<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Preparar SELECT
$stmt = $db->prepare("
    SELECT u.id_usuario, u.nombre_completo, u.correo, u.telefono, 
           u.autenticacion_dos_factores, u.activo, u.id_rol, r.nombre_rol, u.fecha_registro
    FROM Usuario u
    LEFT JOIN Rol r ON u.id_rol = r.id_rol
");
$stmt->execute();
$result = $stmt->get_result();

$usuarios = [];
while ($row = $result->fetch_assoc()) {
  $usuarios[] = $row;
}

$stmt->close();

echo json_encode($usuarios);
