<?php require_once 'encabezado.php'; ?>
<script src="controlador/angular.min.js"></script>
<script src="controlador/capacitaciones.js"></script>

<body ng-app="app" ng-controller="CapacitacionesCtrl" class="container-configuracion">

    <div class="container">

        <!-- Barra de búsqueda -->
        <form>
            <div class="div-buscador input-group w-100">
                <input class="form-control buscador" type="text" name="buscador" id="buscador" placeholder="Buscar">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">🔎
                        <i class="glyphicon glyphicon-search bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabla de Capacitaciones -->
        <div class="table-container mt-4">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="nombre">Tema</th>
                        <th>Descripción</th>
                        <th>Fechas</th>
                        <th>Modalidad</th>
                        <th>Asistentes</th>
                        <th>Constancias</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <tr ng-repeat="c in capacitaciones | filter:buscar">
                        <td>{{c.id_capacitacion}}</td>
                        <td>{{c.tema}}</td>
                        <td>{{c.descripcion}}</td>
                        <td>{{c.fecha_inicio}} <br> <small class="text-muted">{{c.fecha_fin}}</small></td>
                        <td>{{c.tipo_modalidad}}</td>
                        <td>
                            <a href="asistencia.php">
                                <button class="btn btn-primary">👥</button>
                            </a>
                        </td>
                        <td>
                            <button class="btn btn-success" ng-click="listarConstancias(c.id_capacitacion)">📄</button>

                        </td>

                        <td class="acciones">
                            <button class="btn btn-success btn-sm" ng-click="seleccionar(c)">✏️ Modificar</button>
                            <button class="btn btn-danger btn-sm" ng-click="eliminar(c)">🗑️ Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Botón nuevo -->
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            ➕ Nueva Capacitación
        </button>

        <!-- Modal Agregar -->
        <div class="modal fade" id="modalAgregar" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Registrar nueva capacitación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form ng-submit="guardar()">

                            <label>Tema</label>
                            <input type="text" class="form-control mb-2" ng-model="nuevo.tema" required>

                            <label>Descripción</label>
                            <textarea class="form-control mb-2" ng-model="nuevo.descripcion" required></textarea>

                            <div class="row">
                                <div class="col">
                                    <label>Fecha inicio</label>
                                    <input type="date" class="form-control mb-2" ng-model="nuevo.fecha_inicio" required>
                                </div>
                                <div class="col">
                                    <label>Fecha fin</label>
                                    <input type="date" class="form-control mb-2" ng-model="nuevo.fecha_fin" required>
                                </div>
                            </div>

                            <label>Modalidad</label>
                            <select class="form-control mb-2" ng-model="nuevo.tipo_modalidad" required>
                                <option value="Presencial">Presencial</option>
                                <option value="Virtual">Virtual</option>
                                <option value="Mixta">Mixta</option>
                            </select>

                            <button type="submit" class="btn btn-success w-100">Guardar</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal Modificar -->
        <div class="modal fade" id="modalEditar" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Modificar capacitación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form ng-submit="modificar()">

                            <label>Tema</label>
                            <input type="text" class="form-control mb-2" ng-model="capacitacionMod.tema" required>

                            <label>Descripción</label>
                            <textarea class="form-control mb-2" ng-model="capacitacionMod.descripcion" required></textarea>

                            <div class="row">
                                <div class="col">
                                    <label>Fecha inicio</label>
                                    <input type="date" class="form-control mb-2" ng-model="capacitacionMod.fecha_inicio" required>
                                </div>
                                <div class="col">
                                    <label>Fecha fin</label>
                                    <input type="date" class="form-control mb-2" ng-model="capacitacionMod.fecha_fin" required>
                                </div>
                            </div>

                            <label>Modalidad</label>
                            <select class="form-control mb-3" ng-model="capacitacionMod.tipo_modalidad" required>
                                <option value="Presencial">Presencial</option>
                                <option value="Virtual">Virtual</option>
                                <option value="Mixta">Mixta</option>
                            </select>

                            <button type="submit" class="btn btn-success w-100">Guardar cambios</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- MODAL CONSTANCIAS -->
        <div class="modal fade" id="modalConstancias">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Constancias Disponibles</h5>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="u in constancias track by $index">
                                    <td>{{u.nombre_completo}}</td>
                                    <td>
                                        <button class="btn btn-info" ng-click="descargarConstancia(u)">Descargar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>