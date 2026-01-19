<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'encabezado.php';
require_once '../api/conexion.php';

/*  FILTROS*/
$categoria = $_GET['categoria'] ?? '';
$dominio   = $_GET['dominio'] ?? '';
$nivel     = $_GET['nivel'] ?? '';

$where = "1=1";
$params = [];
$types = "";

if ($categoria !== '') {
    $where .= " AND r.categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}
if ($dominio !== '') {
    $where .= " AND r.dominio = ?";
    $params[] = $dominio;
    $types .= "s";
}
if ($nivel !== '') {
    $where .= " AND r.nivel_riesgo = ?";
    $params[] = $nivel;
    $types .= "s";
}

/* CONSULTA*/
$sql = "
SELECT 
    u.id_usuario,
    u.nombre_completo,
    COUNT(DISTINCT e.id_evaluacion) AS total_evaluaciones,
    MAX(e.id_evaluacion) AS id_evaluacion,
    CASE MAX(
        CASE r.nivel_riesgo
            WHEN 'Muy alto' THEN 4
            WHEN 'Alto' THEN 3
            WHEN 'Medio' THEN 2
            WHEN 'Bajo' THEN 1
            ELSE 0
        END
    )
        WHEN 4 THEN 'Muy alto'
        WHEN 3 THEN 'Alto'
        WHEN 2 THEN 'Medio'
        WHEN 1 THEN 'Bajo'
        ELSE 'Nulo'
    END AS nivel_predominante
FROM Usuario u
INNER JOIN Evaluacion e ON e.id_usuario = u.id_usuario
INNER JOIN Resultado r ON r.id_evaluacion = e.id_evaluacion
WHERE $where
GROUP BY u.id_usuario, u.nombre_completo
ORDER BY u.nombre_completo
";

$stmt = $db->prepare($sql);
if (!$stmt) {
    die("❌ Error SQL: " . $db->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$usuarios = $stmt->get_result();
?>

<div class="container mt-4">
    <h3 class="mb-4">📊 Reportes NOM-035</h3>

    <!-- FILTROS -->
    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-4">
            <label>Categoría</label>
            <select name="categoria" class="form-select">
                <option value="">Todas</option>
                <option value="Ambiente de trabajo" <?= $categoria == 'Ambiente de trabajo' ? 'selected' : '' ?>>Ambiente de trabajo</option>
                <option value="Factores propios de la actividad" <?= $categoria == 'Factores propios de la actividad' ? 'selected' : '' ?>>Factores propios</option>
                <option value="Organización del tiempo de trabajo" <?= $categoria == 'Organización del tiempo de trabajo' ? 'selected' : '' ?>>Organización del tiempo</option>
                <option value="Liderazgo y relaciones" <?= $categoria == 'Liderazgo y relaciones' ? 'selected' : '' ?>>Liderazgo y relaciones</option>
            </select>
        </div>

        <div class="col-md-4">
            <label>Dominio</label>
            <select name="dominio" class="form-select">
                <option value="">Todos</option>
                <option value="Carga de trabajo" <?= $dominio == 'Carga de trabajo' ? 'selected' : '' ?>>Carga de trabajo</option>
                <option value="Jornada de trabajo" <?= $dominio == 'Jornada de trabajo' ? 'selected' : '' ?>>Jornada de trabajo</option>
                <option value="Interferencia trabajo-familia" <?= $dominio == 'Interferencia trabajo-familia' ? 'selected' : '' ?>>Interferencia trabajo-familia</option>
                <option value="Violencia" <?= $dominio == 'Violencia' ? 'selected' : '' ?>>Violencia</option>
            </select>
        </div>

        <div class="col-md-4">
            <label>Nivel de riesgo</label>
            <select name="nivel" class="form-select">
                <option value="">Todos</option>
                <option value="Bajo" <?= $nivel == 'Bajo' ? 'selected' : '' ?>>Bajo</option>
                <option value="Medio" <?= $nivel == 'Medio' ? 'selected' : '' ?>>Medio</option>
                <option value="Alto" <?= $nivel == 'Alto' ? 'selected' : '' ?>>Alto</option>
                <option value="Muy alto" <?= $nivel == 'Muy alto' ? 'selected' : '' ?>>Muy alto</option>
            </select>
        </div>

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">🔍 Filtrar</button>
            <a href="listaReportes.php" class="btn btn-secondary">🔄 Limpiar</a>
        </div>
    </form>

    <!-- BOTONES GENERALES -->
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a class="btn btn-danger"
           target="_blank"
           href="../api/reportes/reporte_general_pdf.php?<?= http_build_query($_GET) ?>">
            📄 PDF General
        </a>

        <a class="btn btn-success"
           target="_blank"
           href="../api/reportes/reporte_general_excel.php?<?= http_build_query($_GET) ?>">
            📊 Excel General
        </a>

        <a class="btn btn-warning"
           target="_blank"
           href="../api/reportes/reporte_general_dominio_pdf.php?<?= http_build_query($_GET) ?>">
            📘 PDF por Dominio
        </a>

        <a class="btn btn-info"
           target="_blank"
           href="../api/reportes/reporte_general_categoria_pdf.php?<?= http_build_query($_GET) ?>">
            📂 PDF por Categoría
        </a>
    </div>

    <!-- TABLA -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Total evaluaciones</th>
                <th>Nivel predominante</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($usuarios->num_rows > 0): ?>
                <?php while ($u = $usuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?= $u['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                        <td class="text-center"><?= $u['total_evaluaciones'] ?></td>
                        <td class="text-center">
                            <span class="badge bg-<?=
                                $u['nivel_predominante'] == 'Muy alto' ? 'danger' :
                                ($u['nivel_predominante'] == 'Alto' ? 'warning' :
                                ($u['nivel_predominante'] == 'Medio' ? 'info' :
                                ($u['nivel_predominante'] == 'Bajo' ? 'success' : 'secondary')))
                            ?>">
                                <?= $u['nivel_predominante'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a target="_blank"
                               class="btn btn-sm btn-danger"
                               href="../api/reportes/reporte_individual_pdf.php?id_evaluacion=<?= $u['id_evaluacion'] ?>">
                                📄 PDF
                            </a>
                            <a target="_blank"
                               class="btn btn-sm btn-success"
                               href="../api/reportes/reporte_individual_excel.php?id_evaluacion=<?= $u['id_evaluacion'] ?>">
                                📊 Excel
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        No se encontraron resultados
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
