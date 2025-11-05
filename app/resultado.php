<?php
require_once 'encabezado.php';
require_once '../api/conexion.php';
session_start();

if (!isset($_SESSION['id'])) {
    die("⚠️ Debes iniciar sesión para ver tus resultados.");
}
$idUsuario = $_SESSION['id'];

// ============================
// 1️⃣ Obtener última evaluación
// ============================
$sqlEval = "SELECT id_evaluacion, id_cuestionario, estado, fecha_aplicacion 
            FROM Evaluacion 
            WHERE id_usuario = ? 
            ORDER BY fecha_aplicacion DESC LIMIT 1";
$stmt = $db->prepare($sqlEval);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$eval = $stmt->get_result()->fetch_assoc();

if (!$eval) {
    die("⚠️ No se encontraron evaluaciones registradas.");
}

$idEvaluacion = $eval['id_evaluacion'];
$idCuestionario = $eval['id_cuestionario'];
$nivelGlobal = $eval['estado'];
$fecha = date("d/m/Y H:i", strtotime($eval['fecha_aplicacion']));

// ============================
// 2️⃣ Obtener nombre del cuestionario
// ============================
$sqlCuest = "SELECT nombre FROM Cuestionario WHERE id_cuestionario = ?";
$stmtC = $db->prepare($sqlCuest);
$stmtC->bind_param("i", $idCuestionario);
$stmtC->execute();
$cuest = $stmtC->get_result()->fetch_assoc();
$nombreCuestionario = $cuest['nombre'] ?? 'Cuestionario';

// ============================
// 3️⃣ Obtener resultados detallados
// ============================
$sqlRes = "SELECT categoria, dominio, dimension, puntaje_obtenido, nivel_riesgo 
           FROM Resultado 
           WHERE id_evaluacion = ? 
           ORDER BY categoria, dominio, dimension";
$stmtR = $db->prepare($sqlRes);
$stmtR->bind_param("i", $idEvaluacion);
$stmtR->execute();
$resultados = $stmtR->get_result();

$datos = [];
while ($row = $resultados->fetch_assoc()) {
    $categoria = $row['categoria'];
    if (!isset($datos[$categoria])) $datos[$categoria] = ['puntaje_total' => 0, 'niveles' => [], 'dominios' => []];

    $datos[$categoria]['puntaje_total'] += $row['puntaje_obtenido'];
    $datos[$categoria]['niveles'][] = $row['nivel_riesgo'];
    $datos[$categoria]['dominios'][] = [
        'dominio' => $row['dominio'],
        'dimension' => $row['dimension'],
        'puntaje' => $row['puntaje_obtenido'],
        'riesgo' => $row['nivel_riesgo']
    ];
}

// ============================
// 4️⃣ Convertir a JSON para gráfico
// ============================
$labels = [];
$valores = [];
$niveles = [];

foreach ($datos as $cat => $info) {
    $labels[] = $cat;
    $valores[] = $info['puntaje_total'];

    $nivelesCat = ['Nulo'=>0, 'Bajo'=>1, 'Medio'=>2, 'Alto'=>3, 'Muy alto'=>4];
    $maxNivel = 'Nulo';
    foreach ($info['niveles'] as $nivel) {
        if ($nivelesCat[$nivel] > $nivelesCat[$maxNivel]) {
            $maxNivel = $nivel;
        }
    }
    $niveles[] = $maxNivel;
}

$labelsJson = json_encode($labels, JSON_UNESCAPED_UNICODE);
$valoresJson = json_encode($valores);
$nivelesJson = json_encode($niveles, JSON_UNESCAPED_UNICODE);

// ============================
// 5️⃣ Recomendaciones automáticas (Tabla 7 NOM-035)
// ============================
function obtenerRecomendacion($nivelGlobal) {
    switch ($nivelGlobal) {
        case 'Muy alto':
            return "⚠️ <strong>Nivel Muy Alto:</strong> Se requiere un análisis profundo por categoría y dominio, 
            elaborar un <strong>Programa de Intervención</strong> que incluya:
            <ul>
                <li>Evaluaciones específicas (cuantitativas, cualitativas o clínicas).</li>
                <li>Campañas de sensibilización.</li>
                <li>Revisión de la política de prevención de riesgos psicosociales.</li>
                <li>Fortalecer programas de prevención y difusión interna.</li>
            </ul>";
        case 'Alto':
            return "⚠️ <strong>Nivel Alto:</strong> Se debe analizar cada categoría y dominio e implementar un 
            <strong>Programa de Intervención</strong> que incluya:
            <ul>
                <li>Evaluación específica de riesgos psicosociales.</li>
                <li>Campañas de sensibilización y reforzamiento de la política de prevención.</li>
                <li>Programas de entorno organizacional favorable y prevención de violencia laboral.</li>
            </ul>";
        case 'Medio':
            return "⚠️ <strong>Nivel Medio:</strong> Es necesario revisar y reforzar la política de prevención de 
            riesgos psicosociales mediante un <strong>Programa de Intervención</strong>, promoviendo un entorno organizacional favorable.";
        case 'Bajo':
            return "ℹ️ <strong>Nivel Bajo:</strong> Se recomienda una mayor difusión de la política de prevención de 
            riesgos psicosociales y continuar fortaleciendo la promoción de un entorno laboral saludable.";
        default:
            return "✅ <strong>Nivel Nulo o Despreciable:</strong> No se requieren medidas adicionales. Mantener las prácticas actuales.";
    }
}
$recomendacionHTML = obtenerRecomendacion($nivelGlobal);
?>

<body class="container py-4">
    <h2 class="text-center mb-4"><?php echo htmlspecialchars($nombreCuestionario); ?></h2>
    <p class="text-center">Fecha de aplicación: <?php echo $fecha; ?></p>
    <p class="text-center fs-5">
        <strong>Resultado Global: </strong>
        <span class="badge 
            <?php
            switch($nivelGlobal){
                case 'Muy alto': echo 'bg-danger'; break;
                case 'Alto': echo 'bg-warning text-dark'; break;
                case 'Medio': echo 'bg-info text-dark'; break;
                case 'Bajo': echo 'bg-success'; break;
                default: echo 'bg-secondary';
            }
            ?>">
            <?php echo strtoupper($nivelGlobal); ?>
        </span>
    </p>

    <hr>

    <h4>Resultados por Categoría</h4>
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Categoría</th>
                    <th>Puntaje Total</th>
                    <th>Nivel de Riesgo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $nivelesCat = ['Nulo'=>0, 'Bajo'=>1, 'Medio'=>2, 'Alto'=>3, 'Muy alto'=>4];
                foreach ($datos as $cat => $info) {
                    $maxNivel = 'Nulo';
                    foreach ($info['niveles'] as $nivel) {
                        if ($nivelesCat[$nivel] > $nivelesCat[$maxNivel]) $maxNivel = $nivel;
                    }

                    echo "<tr>
                        <td><strong>$cat</strong></td>
                        <td>{$info['puntaje_total']}</td>
                        <td>$maxNivel</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <hr>

    <h4>Detalle por Dominio y Dimensión</h4>
    <?php foreach ($datos as $cat => $info): ?>
        <h5 class="mt-3 text-primary"><?php echo $cat; ?></h5>
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Dominio</th>
                    <th>Dimensión</th>
                    <th>Puntaje</th>
                    <th>Nivel de Riesgo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($info['dominios'] as $d): ?>
                    <tr>
                        <td><?php echo $d['dominio']; ?></td>
                        <td><?php echo $d['dimension']; ?></td>
                        <td><?php echo $d['puntaje']; ?></td>
                        <td><?php echo $d['riesgo']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <hr>

    <h4 class="mt-4">Visualización Gráfica</h4>
    <canvas id="graficoCategorias" height="150"></canvas>

    <hr>

    <h4 class="mt-4 text-danger">Recomendaciones según nivel de riesgo</h4>
    <div class="alert alert-light border border-secondary" role="alert">
        <?php echo $recomendacionHTML; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = <?php echo $labelsJson; ?>;
        const dataValores = <?php echo $valoresJson; ?>;
        const dataNiveles = <?php echo $nivelesJson; ?>;

        const colores = {
            'Nulo': '#28a745',
            'Bajo': '#6fcf97',
            'Medio': '#f2c94c',
            'Alto': '#f2994a',
            'Muy alto': '#eb5757'
        };

        const backgroundColors = dataNiveles.map(n => colores[n] || '#ccc');

        new Chart(document.getElementById('graficoCategorias'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Puntaje total por categoría',
                    data: dataValores,
                    backgroundColor: backgroundColors,
                    borderColor: '#333',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `Puntaje: ${ctx.parsed.y} (${dataNiveles[ctx.dataIndex]})`
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
