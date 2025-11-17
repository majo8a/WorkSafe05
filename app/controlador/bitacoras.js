var app = angular.module("app", []);

app.controller("BitacoraCtrl", function ($scope, $http) {
  $scope.usuarios = [];

  // Cargar usuarios con estado y fecha de evaluación
  $scope.cargarUsuarios = function () {
    $http.get("../api/usuarios_estado.php").then(
      function (response) {
        console.log(response.data); // Debug
        $scope.usuarios = response.data;
      },
      function (error) {
        console.error("Error al cargar usuarios:", error);
      }
    );
  };

  $scope.cargarUsuarios();
});
