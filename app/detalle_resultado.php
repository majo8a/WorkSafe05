<?php
require_once 'encabezado.php';
require_once '../api/conexion.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Solo Psicólogo 
$idUsuarioSesion = $_SESSION['id_usuario'] ?? $_SESSION['id'] ?? null;
$idRolSesion = $_SESSION['role'] ?? $_SESSION['role'] ?? null;
if (!$idUsuarioSesion) die("⚠️ Debes iniciar sesión.");
if ((int)$idRolSesion !== 2) die("⚠️ Acceso restringido.");

$idEvaluacion = isset($_GET['id_evaluacion']) ? (int)$_GET['id_evaluacion'] : 0;
if (!$idEvaluacion) die("⚠️ Evaluación no especificada.");

//  Obtener datos básicos de la evaluación
$sqlEval = "SELECT e.id_evaluacion, e.id_cuestionario, e.id_usuario, e.fecha_aplicacion,
                   u.nombre_completo AS nombre_evaluado, c.nombre AS nombre_cuestionario
            FROM Evaluacion e
            LEFT JOIN Usuario u ON u.id_usuario = e.id_usuario
            LEFT JOIN Cuestionario c ON c.id_cuestionario = e.id_cuestionario
            WHERE e.id_evaluacion = ? LIMIT 1";
$stmt = $db->prepare($sqlEval);
$stmt->bind_param("i", $idEvaluacion);
$stmt->execute();
$eval = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$eval) die("⚠️ Evaluación no encontrada.");

// Obtener resultados del cuestionario
$sqlRes = "SELECT categoria, dominio, dimension, puntaje_obtenido, nivel_riesgo
           FROM Resultado
           WHERE id_evaluacion = ?
           ORDER BY categoria, dominio, dimension";
$stmtR = $db->prepare($sqlRes);
$stmtR->bind_param("i", $idEvaluacion);
$stmtR->execute();
$res = $stmtR->get_result();

// Estructura de datos
$datos = [];
$nivelGlobal = 'Desconocido';
$puntajeGlobal = 0;

while ($row = $res->fetch_assoc()) {
    $categoria = $row['categoria'] ?: 'Sin categoría';
    $nivel = $row['nivel_riesgo'] ?: 'Desconocido';
    $puntaje = (int)$row['puntaje_obtenido'];

    // Nivel global guardado aparte
    if (mb_strtoupper($categoria, 'UTF-8') === 'GLOBAL') {
        $nivelGlobal = $nivel;
        $puntajeGlobal = $puntaje;
        continue;
    }

    if (!isset($datos[$categoria])) {
        $datos[$categoria] = ['puntaje_total' => 0, 'niveles' => [], 'dominios' => []];
    }

    $datos[$categoria]['puntaje_total'] += $puntaje;
    $datos[$categoria]['niveles'][] = $nivel;
    $datos[$categoria]['dominios'][] = [
        'dominio' => $row['dominio'] ?: 'Sin dominio',
        'dimension' => $row['dimension'] ?: 'Sin dimensión',
        'puntaje' => $puntaje,
        'riesgo' => $nivel
    ];
}
$stmtR->close();

// Preparar datos para gráfica
$labels = [];
$valores = [];
$niveles = [];
$nivelesCat = ['Nulo' => 0, 'Bajo' => 1, 'Medio' => 2, 'Alto' => 3, 'Muy alto' => 4];

foreach ($datos as $cat => $info) {
    $labels[] = $cat;
    $valores[] = $info['puntaje_total'];
    $maxNivel = 'Nulo';
    foreach ($info['niveles'] as $nivel) {
        if (!isset($nivelesCat[$nivel])) continue;
        if ($nivelesCat[$nivel] > $nivelesCat[$maxNivel]) $maxNivel = $nivel;
    }
    $niveles[] = $maxNivel;
}

$labelsJson = json_encode($labels, JSON_UNESCAPED_UNICODE);
$valoresJson = json_encode($valores);
$nivelesJson = json_encode($niveles, JSON_UNESCAPED_UNICODE);

// Función de recomendaciones
function obtenerRecomendacion($nivelGlobal)
{
    switch ($nivelGlobal) {
        case 'Muy alto':
            return "⚠️ <strong>Nivel Muy Alto:</strong> Se requiere un análisis profundo por categoría y dominio. Elaborar un <strong>Programa de Intervención</strong> con evaluaciones específicas, campañas de sensibilización y revisión de políticas.";
        case 'Alto':
            return "⚠️ <strong>Nivel Alto:</strong> Analizar cada categoría y dominio, e implementar un Programa de Intervención y campañas de sensibilización.";
        case 'Medio':
            return "⚠️ <strong>Nivel Medio:</strong> Revisar y reforzar la política de prevención de riesgos psicosociales mediante un Programa de Intervención.";
        case 'Bajo':
            return "ℹ️ <strong>Nivel Bajo:</strong> Mantener políticas de prevención y promoción de un entorno organizacional favorable.";
        default:
            return "✅ <strong>Nivel Nulo o Despreciable:</strong> No se requieren medidas adicionales.";
    }
}
$recomendacionHTML = obtenerRecomendacion($nivelGlobal);
?>

<body>
  <a href="resultados_id.php?id_cuestionario=<?php echo (int)$eval['id_cuestionario']; ?>" 
   class="btn-volver btn btn-sm btn-secondary">
   ← Volver
</a>

    <h3 class="num-evaluacion">Evaluación #<?php echo $idEvaluacion; ?></h3>
    <!-- <a href="resultados_admin.php" class="btn btn-sm btn-secondary mb-3">← Volver</a> -->
    <p>
    <div class="container">
        <div class="datos-resultado">
            <strong>Cuestionario:</strong> <?php echo htmlspecialchars($eval['nombre_cuestionario']); ?><br>
            <strong>Usuario evaluado:</strong> <?php echo htmlspecialchars($eval['nombre_evaluado']); ?><br>
            <strong>Fecha de aplicación:</strong> <?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($eval['fecha_aplicacion']))); ?><br>
            <strong>Puntaje total:</strong> <?php echo $puntajeGlobal; ?><br>
            <strong>Nivel Global:</strong>
            <span class="fw-bold 
            <?php
            echo match ($nivelGlobal) {
                'Muy alto' => 'text-danger',
                'Alto' => 'text-warning',
                'Medio' => 'text-info',
                'Bajo' => 'text-success',
                default => 'text-muted'
            };
            ?>">
                <?php echo htmlspecialchars($nivelGlobal ?: 'Desconocido'); ?>
            </span>
            </p>
        </div>

        <hr>
        <h4 class="titulo-resultados">Resultados por Categoría</h4>
        <div class="table-responsive mb-3">
            <table class="table table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Categoría</th>
                        <th>Puntaje</th>
                        <th>Nivel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos as $cat => $info):
                        $maxNivel = 'Nulo';
                        foreach ($info['niveles'] as $nivel) {
                            if (!isset($nivelesCat[$nivel])) continue;
                            if ($nivelesCat[$nivel] > $nivelesCat[$maxNivel]) $maxNivel = $nivel;
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cat); ?></td>
                            <td><?php echo (int)$info['puntaje_total']; ?></td>
                            <td>
                                <span class="<?php
                                                echo match ($maxNivel) {
                                                    'Muy alto' => 'badge bg-danger',
                                                    'Alto' => 'badge bg-warning text-dark',
                                                    'Medio' => 'badge bg-info text-dark',
                                                    'Bajo' => 'badge bg-success',
                                                    default => 'badge bg-secondary'
                                                };
                                                ?>"><?php echo htmlspecialchars($maxNivel); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h4 class="titulo-resultados">Detalle por Dominio y Dimensión</h4>
        <?php foreach ($datos as $cat => $info): ?>
            <h5 class="subtitulo-resultados mt-3"><?php echo htmlspecialchars($cat); ?></h5>
            <table class="table table-sm table-bordered mb-3">
                <thead class="table-light text-center">
                    <tr>
                        <th>Dominio</th>
                        <th>Dimensión</th>
                        <th>Puntaje</th>
                        <th>Nivel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($info['dominios'] as $d): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($d['dominio']); ?></td>
                            <td><?php echo htmlspecialchars($d['dimension']); ?></td>
                            <td class="text-center"><?php echo (int)$d['puntaje']; ?></td>
                            <td class="text-center">
                                <span class="<?php
                                                echo match ($d['riesgo']) {
                                                    'Muy alto' => 'badge bg-danger',
                                                    'Alto' => 'badge bg-warning text-dark',
                                                    'Medio' => 'badge bg-info text-dark',
                                                    'Bajo' => 'badge bg-success',
                                                    default => 'badge bg-secondary'
                                                };
                                                ?>"><?php echo htmlspecialchars($d['riesgo']); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <hr>
        <h4 class="titulo-resultados">Visualización</h4>
        <canvas id="graficoCategorias" height="120"></canvas>

        <hr>
        <h4 class="titulo-resultados">Recomendaciones</h4>
        <div class="alert alert-light border"><?php echo $recomendacionHTML; ?></div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const labels = <?php echo $labelsJson; ?>;
            const datos = <?php echo $valoresJson; ?>;
            const niveles = <?php echo $nivelesJson; ?>;
            const colores = {
                'Nulo': '#28a745',
                'Bajo': '#6fcf97',
                'Medio': '#f2c94c',
                'Alto': '#f2994a',
                'Muy alto': '#eb5757'
            };
            const bg = niveles.map(n => colores[n] || '#ccc');

            new Chart(document.getElementById('graficoCategorias'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Puntaje por categoría',
                        data: datos,
                        backgroundColor: bg,
                        borderColor: '#333',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 5
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        </script>
    </div>
</body>

</html>