<?php
require_once 'encabezado.php';
require_once '../api/conexion.php';

// Evitar notice si ya se inició la sesión
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Obtener id de usuario desde la sesión (puede ser 'id' o 'id_usuario' según tu proyecto)
$idUsuarioActual = isset($_SESSION['id']) ? (int)$_SESSION['id'] : (isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : null);

// Inyectamos de forma segura la variable a JS
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

            <!-- Botón solo deshabilitado si el cuestionario fue completado por EL USUARIO ACTUAL -->
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
        // 1) Obtener todos los cuestionarios
        $http.get('../api/cuestionario/consultarCuestionario.php')
          .then(function(response) {
            const cuestionarios = Array.isArray(response.data) ? response.data : [];

            // 2) Obtener las evaluaciones (debería devolver id_evaluacion,id_usuario,id_cuestionario,estado)
            $http.get('../api/evaluacion/consultarEvaluacion.php')
              .then(function(respEval) {
                const evaluaciones = Array.isArray(respEval.data) ? respEval.data : [];

                // 3) Para cada cuestionario, marcar completado solo si existe una evaluación
                //    para EL USUARIO ACTUAL con estado === 'completado'.
                cuestionarios.forEach(cu => {
                  if (idUsuarioActual === null) {
                    // Si no hay usuario en sesión, no bloquear NUNCA
                    cu.completado = false;
                    return;
                  }

                  // Buscar evaluación DEL USUARIO actual para este cuestionario
                  const evalUsuario = evaluaciones.find(e =>
                    Number(e.id_cuestionario) === Number(cu.id_cuestionario) &&
                    Number(e.id_usuario) === Number(idUsuarioActual) &&
                    String(e.estado).toLowerCase() === 'completado'
                  );

                  // Solo bloquear si encontramos esa evaluación con estado 'completado'
                  cu.completado = !!evalUsuario;
                });

                $scope.cuestionarios = cuestionarios;
              }, function(err) {
                console.error('Error al cargar evaluaciones', err);
                // Si hay error al cargar evaluaciones, mostramos cuestionarios sin bloquear
                $scope.cuestionarios = cuestionarios.map(c => (Object.assign({}, c, {
                  completado: false
                })));
              });
          }, function(error) {
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