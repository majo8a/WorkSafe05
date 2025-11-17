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

    <!-- Tabla de Bitacora -->
    <div class="tabla-bitacora table-container mt-3">
      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th class="nombre">Usuario</th>
            <th>Acción</th>
            <th>Módulo</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Objeto</th>
            <th>ID Objeto</th>
            <th>IP Origen</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="b in bitacoras">
            <td>{{b.id_bitacora}}</td>
            <td>{{b.id_usuario}}</td>
            <td>{{b.accion}}</td>
            <td>{{b.modulo}}</td>
            <td>{{b.descripcion}}</td>
            <td>{{b.fecha_evento}}</td>
            <td>{{b.objeto}}</td>
            <td>{{b.id_objeto}}</td>
            <td>{{b.ip_origen}}</td>
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