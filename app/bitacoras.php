<?php require_once 'encabezado.php'; ?>
<script src="controlador/angular.min.js"></script>
<script src="controlador/bitacoras.js"></script>

<body ng-app="app" ng-controller="BitacoraCtrl" class="container-configuracion">
  <?php require_once 'menuAdmin.php'; ?>

  <div class="container">
    <form>
      <div class="div-buscador input-group w-100">
        <input class="form-control buscador" type="text" name="buscador" id="buscador" placeholder="Buscar">
        <div class="input-group-append">
          <button class="btn btn-primary" type="submit">
            <i class="glyphicon glyphicon-search bi bi-search"></i>
          </button>
        </div>
      </div>
    </form>

    <!-- Tabla de evaluación -->
    <div class="tabla-bitacora table-container mt-3">
      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th>ID Usuario</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Cuestionario</th>
            <th>Estado Evaluación</th>
            <th>Fecha Aplicación</th>
          </tr>
        </thead>

        <tbody>
          <tr ng-repeat="u in usuarios">
            <td>{{u.id_usuario}}</td>
            <td>{{u.nombre_completo}}</td>
            <td>{{u.correo}}</td>
            <td>{{u.telefono}}</td>
            <td>{{u.nombre_cuestionario}}</td>
            <td>
              <!-- Sin evaluación -->
              <span ng-if="u.estado === 'Sin evaluación'" class="badge bg-danger">
                Sin evaluación
              </span>

              <!-- Pendiente -->
              <span ng-if="u.estado === 'pendiente'" class="badge bg-warning text-dark">
                Pendiente
              </span>

              <!-- Completada -->
              <span ng-if="u.estado === 'completado' || u.estado === 'finalizada' || u.estado === 'completada'"
                class="badge bg-success">
                Completada
              </span>
            </td>

            <td>{{u.fecha_aplicacion}}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <script>
      // Toggle del sidebar en móviles
      const btnMenu = document.getElementById('btnMenu');
      const sidebar = document.getElementById('sidebar');

      btnMenu.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-open');
      });
    </script>

</body>