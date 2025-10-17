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
    <div class="table-container mt-3">
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
            <th class="acciones">Acciones</th>
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
            <td class="acciones text-center">
              <button type="button" ng-click="seleccionar(b)" class="btn btn-success btn-sm mb-1">
                <span class="glyphicon glyphicon-pencil"></span> Modificar
              </button>
              <button type="button" ng-click="eliminar(b)" class="btn btn-danger btn-sm">
                <span class="glyphicon glyphicon-trash"></span> Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Agregar Bitacora -->
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="myModalLabel">Agregar Registro</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <form class="row g-3" ng-submit="guardar()">
              <div class="col-12 row mb-3">
                <label for="id_usuario" class="col-sm-4 col-form-label">Usuario:</label>
                <div class="col-sm-8">
                  <input type="number" class="form-control" ng-model="bitacora.id_usuario" id="id_usuario" placeholder="ID Usuario" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="accion" class="col-sm-4 col-form-label">Acción:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="bitacora.accion" id="accion" placeholder="Acción" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="modulo" class="col-sm-4 col-form-label">Módulo:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="bitacora.modulo" id="modulo" placeholder="Módulo" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="descripcion" class="col-sm-4 col-form-label">Descripción:</label>
                <div class="col-sm-8">
                  <textarea class="form-control" ng-model="bitacora.descripcion" id="descripcion" placeholder="Descripción" required></textarea>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="objeto" class="col-sm-4 col-form-label">Objeto:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="bitacora.objeto" id="objeto" placeholder="Objeto" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="id_objeto" class="col-sm-4 col-form-label">ID Objeto:</label>
                <div class="col-sm-8">
                  <input type="number" class="form-control" ng-model="bitacora.id_objeto" id="id_objeto" placeholder="ID Objeto" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="ip_origen" class="col-sm-4 col-form-label">IP Origen:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="bitacora.ip_origen" id="ip_origen" placeholder="IP Origen">
                </div>
              </div>

              <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">Agregar</button>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <button type="button" class="btn btn-info btn-lg mt-2" data-bs-toggle="modal" data-bs-target="#myModal">Agregar Registro</button>

    <!-- Modal Modificar Bitacora -->
    <div class="modal fade" id="ModalMod" tabindex="-1" aria-labelledby="ModalModLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="ModalModLabel">Modificar Registro</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <form class="row g-3" ng-submit="modificar()">
              <div class="col-12 row mb-3">
                <label for="id_usuario_mod" class="col-sm-4 col-form-label">Usuario:</label>
                <div class="col-sm-8">
                  <input type="number" class="form-control" ng-model="bitacoraMod.id_usuario" id="id_usuario_mod" placeholder="ID Usuario" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="accion_mod" class="col-sm-4 col-form-label">Acción:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="bitacoraMod.accion" id="accion_mod" placeholder="Acción" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="modulo_mod" class="col-sm-4 col-form-label">Módulo:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="bitacoraMod.modulo" id="modulo_mod" placeholder="Módulo" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="descripcion_mod" class="col-sm-4 col-form-label">Descripción:</label>
                <div class="col-sm-8">
                  <textarea class="form-control" ng-model="bitacoraMod.descripcion" id="descripcion_mod" placeholder="Descripción" required></textarea>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="objeto_mod" class="col-sm-4 col-form-label">Objeto:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="bitacoraMod.objeto" id="objeto_mod" placeholder="Objeto" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="id_objeto_mod" class="col-sm-4 col-form-label">ID Objeto:</label>
                <div class="col-sm-8">
                  <input type="number" class="form-control" ng-model="bitacoraMod.id_objeto" id="id_objeto_mod" placeholder="ID Objeto" required>
                </div>
              </div>

              <div class="col-12 row mb-3">
                <label for="ip_origen_mod" class="col-sm-4 col-form-label">IP Origen:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" ng-model="bitacoraMod.ip_origen" id="ip_origen_mod" placeholder="IP Origen">
                </div>
              </div>

              <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">Guardar Cambios</button>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</body>