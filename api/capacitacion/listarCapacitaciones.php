<?php
require_once '../conexion.php';
header('Content-Type: application/json');

try {
    $sql = "
        SELECT c.*, 
        (SELECT COUNT(*) FROM Confirmacion WHERE id_capacitacion=c.id_capacitacion) AS asistentes,
        (SELECT COUNT(*) 
         FROM Usuario_Documento ud
         INNER JOIN Documento d ON d.id_documento = ud.id_documento
         WHERE d.descripcion LIKE CONCAT('%', c.id_capacitacion, '%')
        ) AS constancias
        FROM Capacitacion c
        ORDER BY c.fecha_inicio DESC
    ";

    $result = $conn->query($sql);

    $capacitaciones = [];

    while ($row = $result->fetch_assoc()) {
        $capacitaciones[] = $row;
    }

    echo json_encode(["success" => true, "data" => $capacitaciones]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
