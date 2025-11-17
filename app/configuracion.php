<?php require_once 'encabezado.php'; ?>
<script src="controlador/angular.min.js"></script>
<script src="controlador/administracion.js"></script>

<body ng-app="app" ng-controller="adminCtrl" class="container-configuracion">

  <?php require_once 'menuAdmin.php'; ?>

  <!-- Contenido principal -->
  <div class="fondo content flex-grow-1">

    <div class="table-container">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Usuario</th>
              <th>Tipo Objeto</th>
              <th>ID Objeto</th>
              <th>Campo</th>
              <th>Valor Antiguo</th>
              <th>Valor Nuevo</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="a in administracion">
              <td>{{a.id_cambio}}</td>
              <td>{{a.id_usuario_responsable}}</td>
              <td>{{a.tipo_objeto}}</td>
              <td>{{a.id_objeto}}</td>
              <td>{{a.campo}}</td>
              <td>{{a.valor_antiguo}}</td>
              <td>{{a.valor_nuevo}}</td>
              <td>{{a.fecha_cambio}}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    const btnMenu = document.getElementById('btnMenu');
    const sidebar = document.getElementById('sidebar');

    if (btnMenu && sidebar) {
      btnMenu.addEventListener('click', () => {
        sidebar.classList.toggle('sidebar-open');
      });
    }
  </script>
</body>
