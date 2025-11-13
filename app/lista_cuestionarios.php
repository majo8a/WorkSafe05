<?php
require_once 'encabezado.php';
require_once '../api/conexion.php';

// Evitar notice si ya se inició la sesión
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$idUsuarioActual = isset($_SESSION['id']) ? (int)$_SESSION['id'] : (isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null);

$idUsuarioJs = json_encode($idUsuarioActual);
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
            <a ng-href="cuestionarios.php?id={{ cu.id_cuestionario }}"
              class="btn w-100"
              ng-class="cu.completado ? 'btn-secondary disabled' : 'btn-primary'"
              ng-disabled="cu.completado">
              {{ cu.completado ? 'Completado' : 'Responder' }}
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

      // id de usuario inyectado desde PHP (null si no hay sesión)
      const idUsuarioActual = <?= $idUsuarioJs ?>;

      function cargarCuestionarios() {
        $http.get('../api/cuestionario/consultarCuestionario.php')
          .then(function(response) {
            const cuestionarios = Array.isArray(response.data) ? response.data : [];

            $http.get('../api/evaluacion/consultarEvaluacion.php')
              .then(function(respEval) {
                const evaluaciones = Array.isArray(respEval.data) ? respEval.data : [];

                cuestionarios.forEach(cu => {
                  if (idUsuarioActual === null) {
                    cu.completado = false;
                    return;
                  }
                  const evalUsuario = evaluaciones.find(e =>
                    Number(e.id_cuestionario) === Number(cu.id_cuestionario) &&
                    Number(e.id_usuario) === Number(idUsuarioActual) &&
                    String(e.estado).toLowerCase() === 'completado'
                  );
                  cu.completado = !!evalUsuario;
                });

                $scope.cuestionarios = cuestionarios;
              }, function(err) {
                console.error('Error al cargar evaluaciones', err);
                $scope.cuestionarios = cuestionarios.map(c => (Object.assign({}, c, {
                  completado: false
                })));
              });
          }, function(error) {
            console.error('Error al cargar cuestionarios', error);
          });
      }

      cargarCuestionarios();

      $interval(cargarCuestionarios, 10000);
    });
  </script>
</body>