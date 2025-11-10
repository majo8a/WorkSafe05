<?php
// resultados_admin.php
require_once 'encabezado.php';
require_once '../api/conexion.php';

// Asegurar sesión
if (session_status() === PHP_SESSION_NONE) session_start();

// Comprobar usuario y rol (Psicólogo -> id_rol = 2)
$idUsuarioSesion = $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
$idRolSesion = $_SESSION['role'] ?? $_SESSION['role'] ?? null;

if (!$idUsuarioSesion) {
    die("⚠️ Debes iniciar sesión.");
}
if ((int)$idRolSesion !== 2) {
    die("⚠️ Acceso restringido. Solo psicólogos (rol 2) pueden ver esta sección.");
}

// 1) Listar cuestionarios que tienen evaluaciones
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
    while ($r = $res->fetch_assoc()) $cuestionarios[] = $r;
}

// Si se solicitó ver evaluaciones de un cuestionario
$idCuestionarioVer = isset($_GET['id_cuestionario']) ? (int)$_GET['id_cuestionario'] : null;
$evaluaciones = [];
if ($idCuestionarioVer) {
    $sqlEval = "
        SELECT e.id_evaluacion, e.id_usuario, u.nombre_completo, e.fecha_aplicacion, e.estado
        FROM Evaluacion e
        INNER JOIN Usuario u ON u.id_usuario = e.id_usuario
        WHERE e.id_cuestionario = ?
        ORDER BY e.fecha_aplicacion DESC
    ";
    $stmt = $db->prepare($sqlEval);
    $stmt->bind_param("i", $idCuestionarioVer);
    $stmt->execute();
    $resE = $stmt->get_result();
    while ($row = $resE->fetch_assoc()) $evaluaciones[] = $row;
    $stmt->close();
}
?>
<body class="container py-4">
    <h2 class="mb-3">Panel de Resultados (Psicólogo)</h2>
    <p class="text-muted">Usuario en sesión: <strong><?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? ''); ?></strong></p>

    <div class="card mb-4">
        <div class="card-header"><strong>Cuestionarios aplicados</strong></div>
        <div class="card-body">
            <?php if (empty($cuestionarios)): ?>
                <div class="alert alert-info">No hay cuestionarios con evaluaciones registradas.</div>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cuestionario</th>
                            <th>Descripción</th>
                            <th>Aplicaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cuestionarios as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($c['descripcion']); ?></td>
                                <td><?php echo (int)$c['aplicadas']; ?></td>
                                <td>
                                    <a class="btn btn-sm btn-primary" href="resultados_admin.php?id_cuestionario=<?php echo $c['id_cuestionario']; ?>">
                                        Ver evaluaciones
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($idCuestionarioVer): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Evaluaciones del cuestionario ID: <?php echo $idCuestionarioVer; ?></strong>
                <a href="resultados_admin.php" class="btn btn-sm btn-secondary">Volver a lista</a>
            </div>
            <div class="card-body">
                <?php if (empty($evaluaciones)): ?>
                    <div class="alert alert-info">No hay evaluaciones para este cuestionario.</div>
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
                            <?php foreach ($evaluaciones as $ev): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ev['nombre_completo']); ?></td>
                                    <td><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($ev['fecha_aplicacion']))); ?></td>
                                    <td><?php echo htmlspecialchars($ev['estado']); ?></td>
                                    <td>
                                        <?php
                                            // Mostrar nivel guardado en estado (si usas otro campo, ajusta)
                                            echo htmlspecialchars($ev['estado']);
                                        ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary" href="detalle_resultado.php?id_evaluacion=<?php echo $ev['id_evaluacion']; ?>">
                                            Ver resultado
                                        </a>
                                        <a class="btn btn-sm btn-outline-secondary" href="generar_reporte_pdf.php?id_evaluacion=<?php echo $ev['id_evaluacion']; ?>" target="_blank">
                                            Descargar PDF
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>
