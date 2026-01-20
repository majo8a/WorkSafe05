<?php
require_once 'encabezado.php';
require_once '../api/conexion.php';

// ================================
// Asegurar sesión
// ================================
if (session_status() === PHP_SESSION_NONE) session_start();

// ================================
// Comprobar usuario y rol
// ================================
$idUsuarioSesion = $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
$idRolSesion     = $_SESSION['role'] ?? $_SESSION['role'] ?? null;

if (!$idUsuarioSesion) {
    die("⚠️ Debes iniciar sesión.");
}
if ((int)$idRolSesion !== 2) {
    die("⚠️ Acceso restringido. Solo psicólogos (rol 2) pueden ver esta sección.");
}

// ================================
// 1) Listar cuestionarios con evaluaciones
// ================================
$sql = "
    SELECT c.id_cuestionario, c.nombre, c.descripcion, COUNT(e.id_evaluacion) AS aplicadas
    FROM Cuestionario c
    INNER JOIN Evaluacion e ON e.id_cuestionario = c.id_cuestionario
    GROUP BY c.id_cuestionario, c.nombre, c.descripcion
    ORDER BY c.fecha_creacion DESC
";
$res = $db->query($sql);

$cuestionarios = [];
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $cuestionarios[] = $r;
    }
}

// ================================
// 2) Evaluaciones de un cuestionario
// ================================
$idCuestionarioVer = isset($_GET['id_cuestionario'])
    ? (int)$_GET['id_cuestionario']
    : null;

$evaluaciones = [];

if ($idCuestionarioVer) {

    $sqlEval = "
        SELECT 
            e.id_evaluacion,
            e.id_usuario,
            u.nombre_completo,
            e.fecha_aplicacion,
            e.estado,
            COALESCE(
                (SELECT r2.nivel_riesgo
                 FROM Resultado r2
                 WHERE r2.id_evaluacion = e.id_evaluacion
                 ORDER BY r2.id_resultado DESC
                 LIMIT 1),
                'No disponible'
            ) AS nivel_global
        FROM Evaluacion e
        INNER JOIN Usuario u ON u.id_usuario = e.id_usuario
        LEFT JOIN Resultado r ON r.id_evaluacion = e.id_evaluacion
        WHERE e.id_cuestionario = ?
        GROUP BY e.id_evaluacion, e.id_usuario, u.nombre_completo, e.fecha_aplicacion, e.estado
        ORDER BY e.fecha_aplicacion DESC
    ";

    $stmt = $db->prepare($sqlEval);
    if (!$stmt) {
        die("❌ Error al preparar la consulta: " . $db->error);
    }

    $stmt->bind_param("i", $idCuestionarioVer);
    $stmt->execute();
    $resE = $stmt->get_result();

    while ($row = $resE->fetch_assoc()) {
        $evaluaciones[] = $row;
    }

    $stmt->close();
}
?>

<?php if ($idCuestionarioVer): ?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Evaluaciones del cuestionario ID: <?php echo $idCuestionarioVer; ?></strong>
        <a href="resultados_admin.php" class="btn btn-sm btn-secondary">Volver a lista</a>
    </div>

    <div class="card-body">
        <?php if (empty($evaluaciones)): ?>
            <div class="alert alert-info">
                No hay evaluaciones para este cuestionario.
            </div>
        <?php else: ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Fecha de aplicación</th>
                        <th>Estado</th>
                        <th>Nivel global</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($evaluaciones as $ev):

                    $estado = strtolower(trim($ev['estado']));
                    // Bloquear acciones si NO está finalizada
                    $bloquearAcciones = in_array($estado, ['pendiente', 'asignado']);
                    $tituloDisabled = $bloquearAcciones
                        ? 'Evaluación no disponible. Estado: ' . ucfirst($estado)
                        : '';
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ev['nombre_completo']); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($ev['fecha_aplicacion'])); ?></td>
                        <td><?php echo htmlspecialchars($ev['estado']); ?></td>

                        <td>
                            <?php if ($bloquearAcciones): ?>
                                <span class="text-muted">—</span>
                            <?php else: ?>
                                <?php echo htmlspecialchars($ev['nivel_global']); ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="botones">
                                <?php if ($bloquearAcciones): ?>
                                    <button
                                        class="btn-contenido btn btn-sm btn-outline-primary"
                                        disabled
                                        title="<?php echo $tituloDisabled; ?>">
                                        Ver resultado
                                    </button>

                                    <button
                                        class="btn-contenido btn btn-sm btn-outline-secondary"
                                        disabled
                                        title="<?php echo $tituloDisabled; ?>">
                                        PDF
                                    </button>

                                    <button
                                        class="btn-contenido btn btn-sm btn-outline-secondary"
                                        disabled
                                        title="<?php echo $tituloDisabled; ?>">
                                        Excel
                                    </button>
                                <?php else: ?>
                                    <a
                                        class="btn-contenido btn btn-sm btn-outline-primary"
                                        href="detalle_resultado.php?id_evaluacion=<?php echo $ev['id_evaluacion']; ?>">
                                        Ver resultado
                                    </a>

                                    <a
                                        class="btn-contenido btn btn-sm btn-outline-secondary"
                                        href="../api/reportes/reporte_individual_pdf.php?id_evaluacion=<?php echo $ev['id_evaluacion']; ?>"
                                        target="_blank">
                                        PDF
                                    </a>

                                    <a
                                        class="btn-contenido btn btn-sm btn-outline-secondary"
                                        href="../api/reportes/reporte_individual_excel.php?id_evaluacion=<?php echo $ev['id_evaluacion']; ?>">
                                        Excel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
