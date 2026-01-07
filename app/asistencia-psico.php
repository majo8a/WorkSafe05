<?php require_once 'encabezado.php'; ?>
<script src="controlador/angular.min.js"></script>
<script src="controlador/confirmaciones.js"></script>

<body ng-app="app" ng-controller="ConfirmacionesCtrl" class="container-configuracion">

    <div class="container">

        <!-- Barra de búsqueda -->
        <form>
            <div class="div-buscador input-group w-100">
                <input class="form-control buscador" type="text" name="buscador" id="buscador" placeholder="Buscar">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"> 🔎
                        <i class="glyphicon glyphicon-search bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabla de Confirmaciones -->
        <div class="table-container mt-4">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ID Usuario</th>
                        <th class="nombre">Usuario</th>
                        <th>ID Capacitación</th>
                        <th>Tipo</th>
                        <th>Fecha Confirmación</th>
                        <th>IP</th>
                        <th>Asistió</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <tr ng-repeat="c in confirmaciones | filter:buscar track by $index">

                        <td>{{c.id_confirmacion}}</td>
                        <td>{{c.id_usuario}}</td>
                        <td>{{c.nombre_completo}}</td>
                        <td>{{c.id_capacitacion}}</td>
                        <td>{{c.tipo_confirmacion}}</td>
                        <td>{{c.fecha_confirmacion}}</td>
                        <td>{{c.ip_registro}}</td>
                        <td>
                            <span ng-if="c.asistio == 1">✔️</span>
                            <span ng-if="c.asistio == 0">❌</span>
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
            ➕ Nueva Confirmación
        </button>

        <!-- Modal Agregar -->
        <div class="modal fade" id="modalAgregar" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Registrar nueva confirmación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form ng-submit="guardar()">

                            <label>ID Usuario</label>
                            <input type="number" class="form-control mb-2" ng-model="nuevo.id_usuario" required>

                            <label>ID Capacitación</label>
                            <input type="number" class="form-control mb-2" ng-model="nuevo.id_capacitacion" required>

                            <label>Tipo de confirmación</label>
                            <select class="form-control mb-2" ng-model="nuevo.tipo_confirmacion" required>
                                <option value="Asistencia">Asistencia</option>
                                <option value="Confirmado">Confirmado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>

                            <label>Fecha de confirmación</label>
                            <input type="datetime-local" class="form-control mb-2" ng-model="nuevo.fecha_confirmacion" required>

                            <label>IP registro</label>
                            <input type="text" class="form-control mb-2" ng-model="nuevo.ip_registro" required>

                            <label>Asistió</label>
                            <select class="form-control mb-2" ng-model="nuevo.asistio" required>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
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
                        <h5 class="modal-title">Modificar confirmación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form ng-submit="modificar()">

                            <label>ID Usuario</label>
                            <input type="number" class="form-control mb-2" ng-model="confirmacionMod.id_usuario" required>

                            <label>ID Capacitación</label>
                            <input type="number" class="form-control mb-2" ng-model="confirmacionMod.id_capacitacion" required>

                            <label>Tipo de confirmación</label>
                            <select class="form-control mb-2" ng-model="confirmacionMod.tipo_confirmacion" required>
                                <option value="Asistencia">Asistencia</option>
                                <option value="Confirmado">Confirmado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>

                            <label>Fecha confirmación</label>
                            <input type="datetime-local" class="form-control mb-2" ng-model="confirmacionMod.fecha_confirmacion" required>

                            <label>IP registro</label>
                            <input type="text" class="form-control mb-2" ng-model="confirmacionMod.ip_registro" required>

                            <label>Asistió</label>
                            <select class="form-control mb-3" ng-model="confirmacionMod.asistio" required>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>

                            <button type="submit" class="btn btn-success w-100">Guardar cambios</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

