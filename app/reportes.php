<?php require_once 'encabezado.php'; ?>
<script src="controlador/angular.min.js"></script>
<script src="controlador/reportes.js"></script>

<body ng-app="app" ng-controller="ReportesCtrl" class="container-configuracion">
    <?php require_once 'menuAdmin.php'; ?>
    <div class="container">

        <!-- Barra de búsqueda -->
        <form>
            <div class="div-buscador input-group w-100">
                <input class="form-control buscador" type="text" id="buscador" placeholder="Buscar">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabla de resultados -->
        <div class="table-container mt-3">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Evaluación</th>
                        <th class="categoria">Categoría</th>
                        <th>Dominio</th>
                        <th>Dimensión</th>
                        <th>Puntaje Obtenido</th>
                        <th>Nivel de Riesgo</th>
                        <th>Interpretación</th>
                        <th>Rango</th>
                        <th class="acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="r in resultados">
                        <td>{{r.id_resultado}}</td>
                        <td>{{r.id_evaluacion}}</td>
                        <td>{{r.categoria}}</td>
                        <td>{{r.dominio}}</td>
                        <td>{{r.dimension}}</td>
                        <td>{{r.puntaje_obtenido}}</td>
                        <td>{{r.nivel_riesgo}}</td>
                        <td>{{r.interpretacion}}</td>
                        <td>{{r.id_rango}}</td>
                        <td class="acciones text-center">
                            <button type="button" ng-click="seleccionar(r)" class="btn btn-success btn-sm mb-1">
                                Modificar
                            </button>
                            <button type="button" ng-click="eliminar(r)" class="btn btn-danger btn-sm">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Botón para agregar -->
        <button type="button" class="btn btn-info btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#myModal">
            Agregar
        </button>

        <!-- Modal Guardar -->
        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Agregar Resultado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form class="row g-3" ng-submit="guardar()">
                            <div class="col-12">
                                <label>ID Evaluación:</label>
                                <input type="number" class="form-control" ng-model="resultado.id_evaluacion" required>
                            </div>
                            <div class="col-12">
                                <label>Categoría:</label>
                                <input type="text" class="form-control" ng-model="resultado.categoria">
                            </div>
                            <div class="col-12">
                                <label>Dominio:</label>
                                <input type="text" class="form-control" ng-model="resultado.dominio">
                            </div>
                            <div class="col-12">
                                <label>Dimensión:</label>
                                <input type="text" class="form-control" ng-model="resultado.dimension">
                            </div>
                            <div class="col-12">
                                <label>Puntaje Obtenido:</label>
                                <input type="number" class="form-control" ng-model="resultado.puntaje_obtenido" required>
                            </div>
                            <div class="col-12">
                                <label>Nivel de Riesgo:</label>
                                <input type="text" class="form-control" ng-model="resultado.nivel_riesgo" required>
                            </div>
                            <div class="col-12">
                                <label>Interpretación:</label>
                                <input type="text" class="form-control" ng-model="resultado.interpretacion" required>
                            </div>
                            <div class="col-12">
                                <label>ID Rango:</label>
                                <input type="number" class="form-control" ng-model="resultado.id_rango">
                            </div>
                            <div class="col-12 text-end mt-2">
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

        <!-- Modal Modificar -->
        <div class="modal fade" id="ModalMod" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Modificar Resultado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form class="row g-3" ng-submit="modificar()">
                            <div class="col-12">
                                <label>ID Evaluación:</label>
                                <input type="number" class="form-control" ng-model="resultadoMod.id_evaluacion" required>
                            </div>
                            <div class="col-12">
                                <label>Categoría:</label>
                                <input type="text" class="form-control" ng-model="resultadoMod.categoria">
                            </div>
                            <div class="col-12">
                                <label>Dominio:</label>
                                <input type="text" class="form-control" ng-model="resultadoMod.dominio">
                            </div>
                            <div class="col-12">
                                <label>Dimensión:</label>
                                <input type="text" class="form-control" ng-model="resultadoMod.dimension">
                            </div>
                            <div class="col-12">
                                <label>Puntaje Obtenido:</label>
                                <input type="number" class="form-control" ng-model="resultadoMod.puntaje_obtenido" required>
                            </div>
                            <div class="col-12">
                                <label>Nivel de Riesgo:</label>
                                <input type="text" class="form-control" ng-model="resultadoMod.nivel_riesgo" required>
                            </div>
                            <div class="col-12">
                                <label>Interpretación:</label>
                                <input type="text" class="form-control" ng-model="resultadoMod.interpretacion" required>
                            </div>
                            <div class="col-12">
                                <label>ID Rango:</label>
                                <input type="number" class="form-control" ng-model="resultadoMod.id_rango">
                            </div>
                            <div class="col-12 text-end mt-2">
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
