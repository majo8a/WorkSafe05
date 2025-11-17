<?php require_once 'encabezado.php'; ?>
<script src="controlador/angular.min.js"></script>
<script src="controlador/usuarios.js "></script>

<body ng-app="app" ng-controller="UsuariosCtrl" class="container-configuracion">
    <?php require_once 'menuAdmin.php'; ?>
    <div class="container">
        <!-- Barra de búsqueda -->
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

        <!-- Tabla de usuarios -->
        <div class="table-container">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="nombre">Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Activo</th>
                        <th>Rol</th>
                        <th>Fecha de registro</th>
                        <th class="acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="u in usuarios">
                        <td>{{u.id_usuario}}</td>
                        <td class="nombre">{{u.nombre_completo}}</td>
                        <td>{{u.correo}}</td>
                        <td>{{u.telefono}}</td>
                        <td>{{u.activo == 1 ? 'Activo' : 'Inactivo'}}</td>
                        <td>{{ u.id_rol == 1 ? 'Administrador' : u.id_rol == 2 ? 'Psicólogo' : u.id_rol == 3 ? 'Usuario' : 'Desconocido' }}
                        </td>
                        <td>{{u.fecha_registro}}</td>
                        <td class="acciones text-center">
                            <button type="button" ng-click="seleccionar(u)" class="btn btn-success btn-sm mb-1">
                                <span class="glyphicon glyphicon-pencil"></span> Modificar
                            </button>
                            <button type="button" ng-click="eliminar(u)" class="btn btn-danger btn-sm">
                                <span class="glyphicon glyphicon-trash"></span> Eliminar
                            </button>
                            <button type="button" ng-click="verHistorial(u)" class="btn btn-info btn-sm">
                                <i class="glyphicon glyphicon-list-alt"></i> Historial
                            </button>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
        <!-- Modal Guardar Usuario -->
        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="myModalLabel">Agregar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form class="row g-3" ng-submit="guardar()">
                            <div class="col-12 row mb-3">
                                <label for="nombre_completo" class="col-sm-4 col-form-label">Nombre Completo:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" ng-model="usuario.nombre_completo" id="nombre_completo" placeholder="Nombre de Usuario" required>
                                </div>
                            </div>

                            <div class="col-12 row mb-3">
                                <label for="correo" class="col-sm-4 col-form-label">Correo:</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" ng-model="usuario.correo" id="correo" placeholder="Correo" required>
                                </div>
                            </div>

                            <div class="col-12 row mb-3">
                                <label for="telefono" class="col-sm-4 col-form-label">Teléfono:</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" ng-model="usuario.telefono" id="telefono" placeholder="Número de teléfono">
                                </div>
                            </div>

                            <div class="col-12 row mb-3">
                                <label for="password" class="col-sm-4 col-form-label">Contraseña:</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" ng-model="usuario.password" id="password" placeholder="Contraseña">
                                </div>
                            </div>

                            <div class="col-12 row mb-3">
                                <label for="id_rol" class="col-sm-4 col-form-label">Rol:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" ng-model="usuario.id_rol" id="id_rol"
                                        style="height: 40px; 
                                        border-radius: 10px; 
                                        padding-left: 12px; 
                                        font-size: 16px; 
                                        appearance: none; 
                                        -webkit-appearance: none; 
                                        -moz-appearance: none; 
                                        background-color: #fff; 
                                        border: 1px solid #ced4da;">
                                        <option value="">Seleccione un rol</option>
                                        <option value="1">Administrador</option>
                                        <option value="2">Psicólogo</option>
                                        <option value="3">Usuario</option>
                                    </select>
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
        <!-- Fin Guardar Usuario -->
        <button type="button" class="btn btn-info btn-lg" data-bs-toggle="modal" data-bs-target="#myModal">Agregar</button>
        <!-- Modal Historial -->
        <div class="modal fade" id="ModalHistorial" tabindex="-1" aria-labelledby="ModalHistorialLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="ModalHistorialLabel">Historial de Evaluaciones</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Información del usuario -->
                        <h5 style="color:black;" class="mb-3">
                            Usuario: {{usuarioHistorial.nombre_completo}}
                            <br>
                            <small class="text-muted">ID: {{usuarioHistorial.id_usuario}}</small>
                        </h5>

                        <!-- Tabla del historial -->
                        <table class="table table-striped table-hover" ng-if="historial.length > 0">
                            <thead>
                                <tr>
                                    <th>ID Evaluación</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th>Fecha Aplicación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="h in historial">
                                    <td>{{h.id_evaluacion}}</td>
                                    <td>{{h.nombre_evaluacion}}</td>
                                    <td>{{h.estado}}</td>
                                    <td>{{h.fecha_aplicacion | date:'yyyy-MM-dd HH:mm'}}</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Mensaje cuando no hay evaluaciones -->
                        <div ng-if="historial.length == 0" class="alert alert-warning text-center">
                            El usuario no tiene evaluaciones registradas.
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>

                </div>
            </div>
        </div>
        <!-- Modal Modificar Usuario -->
        <div class="modal fade" id="ModalMod" tabindex="-1" aria-labelledby="ModalModLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="ModalModLabel">Modificar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form class="row g-3" ng-submit="modificar()">
                            <div class="col-12 row mb-3">
                                <label for="nombre_completo_mod" class="col-sm-4 col-form-label">Nombre Completo:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" ng-model="usuarioMod.nombre_completo" id="nombre_completo_mod" placeholder="Nombre de Usuario" required>
                                </div>
                            </div>

                            <div class="col-12 row mb-3">
                                <label for="correo_mod" class="col-sm-4 col-form-label">Correo:</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" ng-model="usuarioMod.correo" id="correo_mod" placeholder="Correo" required>
                                </div>
                            </div>

                            <div class="col-12 row mb-3">
                                <label for="telefono_mod" class="col-sm-4 col-form-label">Teléfono:</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" ng-model="usuarioMod.telefono" id="telefono_mod" placeholder="Número de teléfono">
                                </div>
                            </div>

                            <div class="col-12 row mb-3">
                                <label for="password_mod" class="col-sm-4 col-form-label">Contraseña:</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" ng-model="usuarioMod.password" id="password_mod" placeholder="Contraseña">
                                </div>
                            </div>

                            <div class="col-12 row mb-3">
                                <label for="activo_mod" class="col-sm-4 col-form-label">Activo:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" ng-model="usuarioMod.activo" id="activo_mod"
                                        style="height: 40px; 
                                        border-radius: 10px; 
                                        padding-left: 12px; 
                                        font-size: 16px; 
                                        appearance: none; 
                                        -webkit-appearance: none; 
                                        -moz-appearance: none; 
                                        background-color: #fff; 
                                        border: 1px solid #ced4da;">
                                        <option value="">Seleccione estado</option>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-12 row mb-3">
                                <label for="id_rol" class="col-sm-4 col-form-label">Rol:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" ng-model="usuario.id_rol" id="id_rol"
                                        style="height: 40px; 
                                        border-radius: 10px; 
                                        padding-left: 12px; 
                                        font-size: 16px; 
                                        appearance: none; 
                                        -webkit-appearance: none; 
                                        -moz-appearance: none; 
                                        background-color: #fff; 
                                        border: 1px solid #ced4da;">
                                        <option value="">Seleccione un rol</option>
                                        <option value="1">Administrador</option>
                                        <option value="2">Psicólogo</option>
                                        <option value="3">Usuario</option>
                                    </select>
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
    <script>
        // Toggle del sidebar en móviles
        const btnMenu = document.getElementById('btnMenu');
        const sidebar = document.getElementById('sidebar');

        btnMenu.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-open');
        });
    </script>
</body>