<?php require_once 'encabezado.php'; ?>
<script src="controlador/angular.min.js"></script>
<script src="controlador/capacitacion.js"></script>

<body class="container-configuracion capacitaciones" ng-app="app" ng-controller="CapacitacionesCtrl">

    <div class="container py-4">

        <h3 class="titulo-blog mb-4">Capacitaciones Disponibles</h3>
        <br>
        <br>

        <!-- LISTA DINÁMICA -->
        <div class="row g-4">

            <div class="col-md-6 col-lg-4"
                ng-repeat="c in capacitaciones | filter:buscar">

                <div class="card p-3 shadow-sm">

                    <h4 class="tema-capacitacion">{{ c.tema }}</h4>
                    <p>{{ c.descripcion }}</p>

                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span>Inicio: {{ c.fecha_inicio | date:'dd/MM/yyyy' }}</span>
                        <span>Fin: {{ c.fecha_fin | date:'dd/MM/yyyy' }}</span>
                    </div>

                    <span class="badge bg-primary mt-3">
                        {{ c.tipo_modalidad }}
                    </span>

                    <!-- BOTÓN CONFIRMAR / YA CONFIRMADO -->
                    <button class="btn mt-3 w-100"
                        ng-class="c.confirmado == 1 ? 'btn-success' : 'btn-primary'"
                        ng-disabled="c.confirmado == 1"
                        ng-click="abrirModal(c)">

                        <span ng-if="c.confirmado == 1">✔ Ya confirmado</span>
                        <span ng-if="c.confirmado != 1">Confirmar asistencia</span>

                    </button>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CONFIRMAR ASISTENCIA -->
    <div class="modal fade" id="modalConfirmar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Confirmar asistencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="color:black">
                    <p><strong>Tema:</strong> {{ modalData.tema }}</p>
                    <p>{{ modalData.descripcion }}</p>
                    <p><strong>Fechas:</strong> {{ modalData.fecha_inicio }} - {{ modalData.fecha_fin }}</p>
                    <p><strong>Modalidad:</strong> {{ modalData.tipo_modalidad }}</p>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" ng-click="confirmarAsistencia()">
                        Confirmar
                    </button>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
