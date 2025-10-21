<?php 
require_once 'encabezado.php';
require_once '../api/conexion.php';

// Consultar los cuestionarios existentes
$query = "SELECT id_cuestionario, nombre, descripcion, version FROM Cuestionario ORDER BY fecha_creacion DESC";
$result = $db->query($query);
?>

<body>
  <div class="container mt-4">
    <div class="row">
      <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <h5 class="card-header">Cuestionario</h5>
            <div class="card-body">
              <h6 class="card-title"><?php echo htmlspecialchars($row['nombre']);  ?></h6>
              <p class="card-text"><?php echo htmlspecialchars($row['descripcion']); ?></p>
              <a href="cuestionarios.php?id=<?php echo $row['id_cuestionario']; ?>" class="btn btn-primary">
                Responder
              </a>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</body>
