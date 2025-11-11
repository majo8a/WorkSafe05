<?php
require_once 'encabezado.php';
require_once '../api/conexion.php';
?>

<body ng-app="appCuestionarios" ng-controller="ctrlCuestionarios">
  <div class="container mt-4">
    <h3 class="mb-4 text-center">Cuestionarios disponibles</h3>

    <div class="row">
      <div class="col-md-4 mb-4" ng-repeat="cu in cuestionarios">
        <div class="card h-100 shadow-sm">
          <h5 class="card-header">Cuestionario</h5>
          <div class="card-body">
            <h6 class="card-title">{{ cu.nombre }}</h6>
            <p class="card-text">{{ cu.descripcion }}</p>

            <!-- Botón deshabilitado si el estado es 'completado' -->
            <a ng-href="cuestionarios.php?id={{ cu.id_cuestionario }}"
              class="btn"
              ng-class="cu.estado === 'completado' ? 'btn-secondary disabled' : 'btn-primary'"
              ng-attr-disabled="{{ cu.estado === 'completado' ? true : undefined }}">
              {{ cu.estado === 'completado' ? 'Completado' : 'Responder' }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="controlador/angular.min.js"></script>
  <script>
    const app = angular.module('appCuestionarios', []);

    app.controller('ctrlCuestionarios', function($scope, $http, $interval) {
      $scope.cuestionarios = [];

      // Cargar cuestionarios y estados
      function cargarCuestionarios() {
        // Obtener cuestionarios
        $http.get('../api/cuestionario/consultarCuestionario.php')
          .then(function(response) {
            const cuestionarios = response.data;

            // Obtener estados de evaluaciones
            $http.get('../api/evaluacion/consultarEvaluacion.php')
              .then(function(respEval) {
                const evaluaciones = respEval.data;

                // Vincular estado a cada cuestionario si existe
                cuestionarios.forEach(cu => {
                  const evalMatch = evaluaciones.find(e => e.id_cuestionario == cu.id_cuestionario);
                  cu.estado = evalMatch ? evalMatch.estado : 'pendiente';
                });

                $scope.cuestionarios = cuestionarios;
              }, function(err) {
                console.error('Error al cargar evaluaciones', err);
                $scope.cuestionarios = cuestionarios;
              });

          }, function(error) {
            console.error('Error al cargar cuestionarios', error);
          });
      }

      // Cargar al iniciar
      cargarCuestionarios();

      // Actualizar cada 10 segundos
      $interval(cargarCuestionarios, 10000);
    });
  </script>
</body>