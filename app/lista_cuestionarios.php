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
            <a ng-href="cuestionarios.php?id={{ cu.id_cuestionario }}" class="btn btn-primary">
              Responder
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="controlador/angular.min.js"></script>
  <script>
    const app = angular.module('appCuestionarios', []);

    app.controller('ctrlCuestionarios', function ($scope, $http, $interval) {
      $scope.cuestionarios = [];

      // Cargar cuestionarios
      function cargarCuestionarios() {
        $http.get('../api/cuestionario/consultarCuestionario.php')
          .then(function (response) {
            $scope.cuestionarios = response.data;
          }, function (error) {
            console.error('Error al cargar cuestionarios', error);
          });
      }

      // Cargar al iniciar
      cargarCuestionarios();

      // Actualizar cada 10 segundos (opcional)
      $interval(cargarCuestionarios, 10000);
    });
  </script>
</body>
