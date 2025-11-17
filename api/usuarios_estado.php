<?php
error_reporting(E_ALL);
require_once 'conexion.php';

// Consulta: obtener estado, fecha y nombre del cuestionario para usuarios con rol 3
$sql = "
    SELECT 
        u.id_usuario,
        u.nombre_completo,
        u.correo,
        u.telefono,
        u.fecha_registro,

        COALESCE(e.estado, 'Sin evaluación') AS estado,
        COALESCE(e.fecha_aplicacion, 'Sin fecha') AS fecha_aplicacion,
        COALESCE(c.nombre, 'Sin cuestionario') AS nombre_cuestionario

    FROM Usuario u

    LEFT JOIN (
        SELECT 
            id_usuario,
            estado,
            fecha_aplicacion,
            id_cuestionario
        FROM Evaluacion
        ORDER BY fecha_aplicacion DESC
    ) e ON u.id_usuario = e.id_usuario

    LEFT JOIN Cuestionario c ON e.id_cuestionario = c.id_cuestionario

    WHERE u.id_rol = 3
    GROUP BY u.id_usuario
";

$stmt = $db->prepare($sql);
$stmt->execute();
$stmt->bind_result(
  $id_usuario,
  $nombre_completo,
  $correo,
  $telefono,
  $fecha_registro,
  $estado,
  $fecha_aplicacion,
  $nombre_cuestionario
);

$arr = array();

while ($stmt->fetch()) {
  $arr[] = array(
    'id_usuario'         => $id_usuario,
    'nombre_completo'    => $nombre_completo,
    'correo'             => $correo,
    'telefono'           => $telefono,
    'fecha_registro'     => $fecha_registro,
    'estado'             => $estado,
    'fecha_aplicacion'   => $fecha_aplicacion,
    'nombre_cuestionario' => $nombre_cuestionario
  );
}

$stmt->close();

echo json_encode($arr);
