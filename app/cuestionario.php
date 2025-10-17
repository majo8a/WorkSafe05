<?php require_once 'encabezado.php'; ?>
<script src="controlador/angular.min.js"></script>
<script src="controlador/cuestionario.js"></script>
<style>
  /* --- ESTILOS GENERALES DE LA TABLA --- */
  .table td,
  .table th {
    white-space: normal !important;
    word-wrap: break-word;
    vertical-align: middle;
  }

  /* Ajuste de columnas con texto largo */
  .table .nombre {
    max-width: 220px;
  }

  .table .descripcion {
    max-width: 350px;
  }

  /* Contenedor de tabla */
  .table-container {
    overflow-x: auto;
  }

  /* --- ESTILOS PARA LA COLUMNA DE ACCIONES --- */
  .acciones {
    width: 180px;
    /* Tamaño fijo para que no se deforme */
    text-align: center;
    vertical-align: middle !important;
  }

  .acciones .btn {
    margin: 3px 0;
    width: 100px;
    /* Todos los botones con el mismo ancho */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
  }

  /* Para pantallas pequeñas: botones más compactos */
  @media (max-width: 768px) {
    .acciones {
      width: auto;
      white-space: nowrap;
    }

    .acciones .btn {
      width: auto;
      padding: 5px 8px;
      font-size: 0.8rem;
    }
  }

  /* Ajuste de textareas en los modales */
  textarea.form-control {
    min-height: 80px;
    resize: vertical;
  }
</style>




<body ng-app="app" ng-controller="CuestionarioCtrl" class="container-configuracion">
  <?php require_once 'menuAdmin.php'; ?>
  <div class="container">

    <!-- Barra de búsqueda -->
    <form>
      <div class="div-buscador input-group w-100 mt-3">
        <input class="form-control buscador" type="text" name="buscador" id="buscador" placeholder="Buscar cuestionario">
        <div class="input-group-append">
          <button class="btn btn-primary" type="submit">
            <i class="glyphicon glyphicon-search bi bi-search"></i>
          </button>
        </div>
      </div>
    </form>

    <!-- Tabla de Cuestionarios -->
    <div class="table-container mt-4">
      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th class="nombre">Nombre</th>
            <th>Descripción</th>
            <th>Versión</th>
            <th>Estado</th>
            <th>Fecha de creación</th>
            <th>Usuario creador</th>
            <th class="acciones">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="c in cuestionarios">
            <td>{{c.id_cuestionario}}</td>
            <td class="nombre">{{c.nombre}}</td>
            <td>{{c.descripcion}}</td>
            <td>{{c.version}}</td>
            <td>{{c.estado}}</td>
            <td>{{c.fecha_creacion}}</td>
            <td>{{c.id_usuario_creador}}</td>
            <td class="acciones text-center">
              <button type="button" ng-click="seleccionar(c)" class="btn btn-success btn-sm mb-1">
                <span class="glyphicon glyphicon-pencil"></span> Modificar
              </button>
              <button type="button" ng-click="eliminar(c)" class="btn btn-danger btn-sm">
                <span class="glyphicon glyphicon-trash"></span> Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Agregar Cuestionario -->
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

          <!-- Header -->
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="myModalLabel">Agregar Cuestionario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <form class="row g-3" ng-submit="guardar()">
              <div class="col-12 row mb-3">
                <label for="nombre" class="col-sm-4 col-form-label">Nombre:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="cuestionario.nombre" id="nombre" placeholder="Nombre del cuestionario" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="descripcion" class="col-sm-4 col-form-label">Descripción:</label>
                <div class="col-sm-8">
                  <textarea class="form-control" ng-model="cuestionario.descripcion" id="descripcion" placeholder="Descripción" required></textarea>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="version" class="col-sm-4 col-form-label">Versión:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="cuestionario.version" id="version" placeholder="Versión del cuestionario" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="estado" class="col-sm-4 col-form-label">Estado:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="cuestionario.estado" id="estado" placeholder="Activo/Inactivo">
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="id_usuario_creador" class="col-sm-4 col-form-label">ID Usuario Creador:</label>
                <div class="col-sm-8">
                  <input type="number" class="form-control" ng-model="cuestionario.id_usuario_creador" id="id_usuario_creador" placeholder="ID del usuario creador">
                </div>
              </div>

              <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">Agregar</button>
              </div>
            </form>
          </div>

          <!-- Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>

        </div>
      </div>
    </div>

    <!-- Botón para abrir modal -->
    <button type="button" class="btn btn-info btn-lg mt-2" data-bs-toggle="modal" data-bs-target="#myModal">Agregar Cuestionario</button>

    <!-- Modal Modificar Cuestionario -->
    <div class="modal fade" id="ModalMod" tabindex="-1" aria-labelledby="ModalModLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

          <!-- Header -->
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="ModalModLabel">Modificar Cuestionario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <form class="row g-3" ng-submit="modificar()">
              <div class="col-12 row mb-3">
                <label for="nombre_mod" class="col-sm-4 col-form-label">Nombre:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="cuestionarioMod.nombre" id="nombre_mod" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="descripcion_mod" class="col-sm-4 col-form-label">Descripción:</label>
                <div class="col-sm-8">
                  <textarea class="form-control" ng-model="cuestionarioMod.descripcion" id="descripcion_mod" required></textarea>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="version_mod" class="col-sm-4 col-form-label">Versión:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="cuestionarioMod.version" id="version_mod" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="estado_mod" class="col-sm-4 col-form-label">Estado:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="cuestionarioMod.estado" id="estado_mod">
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="id_usuario_creador_mod" class="col-sm-4 col-form-label">Usuario Creador:</label>
                <div class="col-sm-8">
                  <input type="number" class="form-control" ng-model="cuestionarioMod.id_usuario_creador" id="id_usuario_creador_mod">
                </div>
              </div>

              <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">Guardar Cambios</button>
              </div>
            </form>
          </div>

          <!-- Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>

        </div>
      </div>
    </div>

  </div>
</body>