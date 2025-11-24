<?php require_once 'encabezado.php'; ?>
<script src="controlador/angular.min.js"></script>
<script src="controlador/cuestionario.js"></script>
<style>
  .table td,
  .table th {
    white-space: normal !important;
    word-wrap: break-word;
    vertical-align: middle;
  }

  .table .nombre {
    max-width: 220px;
  }

  .table .descripcion {
    max-width: 350px;
  }

  .table-container {
    overflow-x: auto;
  }

  .acciones {
    width: 180px;
    text-align: center;
    vertical-align: middle !important;
  }

  .acciones .btn {
    margin: 3px 0;
    width: 100px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
  }

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

  textarea.form-control {
    min-height: 80px;
    resize: vertical;
  }
</style>

<body ng-app="app" ng-controller="CuestionarioCtrl" class="container-configuracion">

  <div class="container">

    <!-- Barra de búsqueda -->
    <div class="div-buscador input-group w-100 mt-3">
      <input class="form-control buscador" type="text" ng-model="buscar" placeholder="Buscar cuestionario">
      <div class="input-group-append">
        <button class="btn btn-primary">🔎<i class="bi bi-search"></i></button>
      </div>
    </div>

    <!-- Tabla de Cuestionarios -->
    <div class="table-container mt-4">
      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Versión</th>
            <th>Estado</th>
            <th>Fecha creación</th>
            <th>Usuario creador</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="c in cuestionarios | filter:buscar">
            <td>{{c.id_cuestionario}}</td>
            <td>{{c.nombre}}</td>
            <td>{{c.descripcion}}</td>
            <td>{{c.version}}</td>
            <td>{{c.estado}}</td>
            <td>{{c.fecha_creacion}}</td>
            <td>{{c.id_usuario_creador}}</td>
            <td class="acciones">
              <button class="btn btn-success btn-sm" ng-click="seleccionar(c)">✏️ Modificar</button>
              <button class="btn btn-danger btn-sm" ng-click="eliminar(c)">🗑️ Eliminar</button>
              <button class="btn btn-info btn-sm" ng-click="verPreguntas(c.id_cuestionario)">👁️ Ver preguntas</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Botón nuevo -->
    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#myModal">➕ Nuevo Cuestionario</button>

    <!-- Modal Agregar -->
    <div class="modal fade" id="myModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5>Nuevo Cuestionario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form ng-submit="guardarTodo()">
              <h6 class="text-primary">Datos del cuestionario</h6>
              <input type="text" class="form-control mb-2" placeholder="Nombre" ng-model="cuestionario.nombre" required>
              <textarea class="form-control mb-2" placeholder="Descripción" ng-model="cuestionario.descripcion" required></textarea>
              <input type="text" class="form-control mb-2" placeholder="Versión" ng-model="cuestionario.version" required>
              <input type="text" class="form-control mb-2" placeholder="Estado (Activo/Inactivo)" ng-model="cuestionario.estado">
              <input type="number" class="form-control mb-3" placeholder="ID usuario creador" ng-model="cuestionario.id_usuario_creador" required>
              <hr>
              <h6 class="text-primary">Preguntas del cuestionario</h6>

              <div class="border p-3 mb-3 rounded bg-light">
                <textarea class="form-control mb-2" placeholder="Texto de la pregunta" ng-model="nuevaPregunta.texto_pregunta"></textarea>
                <select class="form-select mb-2" ng-model="nuevaPregunta.tipo_calificacion">
                  <option value="Likert">Likert</option>
                  <option value="Binaria">Binaria</option>
                  <option value="Texto">Texto</option>
                </select>
                <input type="text" class="form-control mb-2" placeholder="Dimensión" ng-model="nuevaPregunta.dimension">
                <input type="text" class="form-control mb-2" placeholder="Dominio" ng-model="nuevaPregunta.dominio">
                <input type="text" class="form-control mb-2" placeholder="Categoría" ng-model="nuevaPregunta.categoria">
                <input type="text" class="form-control mb-2" placeholder="Grupo aplicación" ng-model="nuevaPregunta.grupo_aplicacion">
                <!-- OPCIONES DE RESPUESTA -->
                <div class="border rounded p-2 bg-white mb-2">
                  <h6>Opciones de respuesta</h6>
                  <div ng-repeat="o in nuevaPregunta.opciones" class="mb-2">
                    <div class="input-group">
                      <input type="text" class="form-control" placeholder="Etiqueta" ng-model="o.etiqueta" required>
                      <input type="number" class="form-control" placeholder="Valor" ng-model="o.valor" required>
                      <button type="button" class="btn btn-danger" ng-click="eliminarOpcion($index)">🗑️</button>
                    </div>
                  </div>
                  <button type="button" class="btn btn-sm btn-secondary" ng-click="agregarOpcion()">➕ Agregar opción</button>
                </div>

                <button type="button" class="btn btn-primary w-100" ng-click="agregarPreguntaTemp()">Agregar pregunta</button>
              </div>

              <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="p in preguntas">
                  {{p.texto_pregunta}} <small class="text-muted">({{p.tipo_calificacion}})</small>
                  <button class="btn btn-danger btn-sm" ng-click="eliminarPreguntaTemp($index)">X</button>
                </li>
              </ul>
              <button type="submit" class="btn btn-success w-100">Guardar Cuestionario</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Modificar Cuestionario-->
    <div class="modal fade" id="ModalMod" tabindex="-1" aria-labelledby="ModalModLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="ModalModLabel">Modificar Cuestionario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <div class="modal-body">
            <!-- FORMULARIO PARA EDITAR CUESTIONARIO -->
            <form ng-submit="modificarCuestionario()">
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" ng-model="cuestionarioMod.nombre" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" ng-model="cuestionarioMod.descripcion" required></textarea>
              </div>
              <div class="row mb-3">
                <div class="col">
                  <label class="form-label">Versión</label>
                  <input type="text" class="form-control" ng-model="cuestionarioMod.version" required>
                </div>
                <div class="col">
                  <label class="form-label">Estado</label>
                  <input type="text" class="form-control" ng-model="cuestionarioMod.estado">
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label">ID Usuario Creador</label>
                <input type="number" class="form-control" ng-model="cuestionarioMod.id_usuario_creador">
              </div>

              <div class="text-end mb-3">
                <button type="submit" class="btn btn-success">Guardar cambios del cuestionario</button>
              </div>
            </form>

            <hr>

            <!-- SECCIÓN PARA AGREGAR PREGUNTA AL CUESTIONARIO ACTUAL -->
            <h6>Agregar nueva pregunta al cuestionario</h6>
            <div class="border rounded p-3 mb-3 bg-light">
              <div class="mb-2">
                <textarea class="form-control" ng-model="nuevaPregunta.texto_pregunta" placeholder="Texto de la pregunta"></textarea>
              </div>
              <div class="row mb-2">
                <div class="col-6">
                  <select class="form-select" ng-model="nuevaPregunta.tipo_calificacion">
                    <option value="Likert">Likert</option>
                    <option value="Binaria">Binaria</option>
                    <option value="Texto">Texto</option>
                  </select>
                  <!-- OPCIONES DE RESPUESTA -->
                  <div class="border rounded p-2 bg-white mb-2">
                    <h6>Opciones de respuesta</h6>
                    <div ng-repeat="o in nuevaPregunta.opciones" class="mb-2">
                      <div class="input-group">
                        <input type="text" class="form-control" placeholder="Etiqueta" ng-model="o.etiqueta" required>
                        <input type="number" class="form-control" placeholder="Valor" ng-model="o.valor" required>
                        <button type="button" class="btn btn-danger" ng-click="eliminarOpcion($index)">🗑️</button>
                      </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" ng-click="agregarOpcion()">➕ Agregar opción</button>
                  </div>

                </div>
                <div class="col-3">
                  <input type="number" class="form-control" ng-model="nuevaPregunta.puntaje_maximo" min="1" placeholder="Valor">
                </div>
                <div class="col-3">
                  <input type="number" class="form-control" ng-model="nuevaPregunta.orden" min="1" placeholder="Orden (opcional)">
                </div>
              </div>
              <div class="row mb-2">
                <div class="col">
                  <input class="form-control" ng-model="nuevaPregunta.dimension" placeholder="Dimensión">
                </div>
                <div class="col">
                  <input class="form-control" ng-model="nuevaPregunta.dominio" placeholder="Dominio">
                </div>
              </div>
              <div class="row mb-2">
                <div class="col">
                  <input class="form-control" ng-model="nuevaPregunta.categoria" placeholder="Categoría">
                </div>
                <div class="col">
                  <input class="form-control" ng-model="nuevaPregunta.grupo_aplicacion" placeholder="Grupo de aplicación">
                </div>
              </div>

              <div class="text-end">
                <button class="btn btn-primary" ng-click="agregarPreguntaModal()">Añadir pregunta</button>
              </div>
            </div>

            <!-- LISTA DE PREGUNTAS DEL CUESTIONARIO-->
            <h6>Preguntas registradas</h6>
            <div ng-if="preguntasDelCuestionario.length==0" class="text-muted mb-2">No hay preguntas aún.</div>
            <table class="table table-striped" ng-if="preguntasDelCuestionario.length>0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Texto</th>
                  <th>Tipo</th>
                  <th>Puntaje</th>
                  <th>Orden</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="p in preguntasDelCuestionario">
                  <td>{{$index+1}}</td>
                  <td>{{p.texto_pregunta}}</td>
                  <td>{{p.tipo_calificacion}}</td>
                  <td>{{p.puntaje_maximo}}</td>
                  <td>{{p.orden}}</td>
                  <td>
                    <button class="btn btn-success btn-sm" ng-click="editarPregunta(p)">✏️</button>
                    <button class="btn btn-danger btn-sm" ng-click="eliminarPregunta(p.id_pregunta)">🗑️</button>
                  </td>
                </tr>
              </tbody>
            </table>

          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ver preguntas -->
    <div class="modal fade" id="modalVerPreguntas" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5>Preguntas del Cuestionario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div ng-if="preguntasDelCuestionario.length==0" class="text-muted">No hay preguntas registradas.</div>
            <table class="table" ng-if="preguntasDelCuestionario.length>0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Texto</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="p in preguntasDelCuestionario">
                  <td>{{$index+1}}</td>
                  <td>{{p.texto_pregunta}}</td>
                  <td>
                    <button class="btn btn-success btn-sm" ng-click="editarPregunta(p)">✏️</button>
                    <button class="btn btn-danger btn-sm" ng-click="eliminarPregunta(p.id_pregunta)">🗑️</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
        </div>
      </div>
    </div>

  </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>