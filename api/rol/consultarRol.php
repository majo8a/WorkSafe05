<?php
error_reporting(E_ALL);
require_once '../conexion.php';

// Preparar SELECT
$stmt = $db->prepare("SELECT id_rol, nombre_rol, descripcion FROM Rol");
$stmt->execute();
$result = $stmt->get_result();

$roles = [];
while ($row = $result->fetch_assoc()) {
  $roles[] = $row;
}

$stmt->close();

echo json_encode($roles);
