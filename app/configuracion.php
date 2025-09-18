<?php require_once 'encabezado.php'; ?>
<script src="controlador/angular.min.js"></script>
<script src="controlador/administracion.js "></script>

<body ng-app="app"ng-controller="adminCtrl" class="container-configuracion">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column p-3">
            <br>
            <h4 class="text-center">Menú</h4>
            <br>
            <a href="#">⚙️ Ajustes Generales</a>
            <a href="#">👥 Usuarios</a>
            <a href="#">📑 Reportes</a>
            <a href="#">📋 Cuestionarios</a>
            <a href="#">🔗 Bitacora</a>
        </div>

        <!-- Contenido principal -->
        <div class="fondo content flex-grow-1">
            <h2 class="titulo-historial">Historial de Movimientos</h2>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th class="tabla-encabezado">ID</th>
                            <th class="tabla-encabezado">Usuario</th>
                            <th class="tabla-encabezado">Tipo Objeto</th>
                            <th class="tabla-encabezado">ID Objeto</th>
                            <th class="tabla-encabezado">Campo</th>
                            <th class="tabla-encabezado">Valor Antiguo</th>
                            <th class="tabla-encabezado">Valor Nuevo</th>
                            <th class="tabla-encabezado">Fecha</th>
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
</body>