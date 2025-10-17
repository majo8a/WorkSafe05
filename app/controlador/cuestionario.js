var App = angular.module("app", []);

App.controller("CuestionarioCtrl", function ($scope, $http) {
  // Objeto para un nuevo cuestionario
  $scope.cuestionario = {};

  // Arreglo de todos los cuestionarios
  $scope.cuestionarios = [];

  // Consultar cuestionarios
  $scope.consultar = function () {
    $http
      .post("../api/cuestionario/consultarCuestionario.php")
      .success(function (data) {
        $scope.cuestionarios = data;
      })
      .error(function () {
        alert("Error al consultar cuestionarios");
      });
  };

  // Consultar al cargar
  $scope.consultar();

  // Guardar nuevo cuestionario
  $scope.guardar = function () {
    $http
      .post("../api/cuestionario/guardarCuestionario.php", $scope.cuestionario)
      .success(function () {
        $scope.cuestionario = {};
        $scope.consultar();

        var modal = bootstrap.Modal.getInstance(
          document.getElementById("myModal")
        );
        modal.hide();
      })
      .error(function () {
        alert("Error al guardar el cuestionario");
      });
  };

  // Objeto para modificar
  $scope.cuestionarioMod = {};

  // Seleccionar para modificar
  $scope.seleccionar = function (c) {
    $scope.cuestionarioMod = angular.copy(c);
    var modal = new bootstrap.Modal(document.getElementById("ModalMod"));
    modal.show();
  };

  // Modificar cuestionario
  $scope.modificar = function () {
    $http
      .post(
        "../api/cuestionario/modificarCuestionario.php",
        $scope.cuestionarioMod
      )
      .success(function () {
        $scope.cuestionarioMod = {};
        $scope.consultar();
        var modal = bootstrap.Modal.getInstance(
          document.getElementById("ModalMod")
        );
        modal.hide();
      })
      .error(function () {
        alert("Error al modificar el cuestionario");
      });
  };

  // Eliminar cuestionario
  $scope.eliminar = function (cuestionario) {
    if (confirm("¿Deseas eliminar este cuestionario?")) {
      $http
        .post("../api/cuestionario/eliminarCuestionario.php", cuestionario)
        .success(function () {
          $scope.consultar();
        })
        .error(function () {
          alert("Error al eliminar el cuestionario");
        });
    }
  };
});

/* BUSCAR CUESTIONARIOS POR NOMBRE, DESCRIPCION, VERSION */
document.addEventListener("keyup", (e) => {
  if (e.target.id === "buscador") {
    if (e.key === "Escape") e.target.value = "";

    document.querySelectorAll("tbody tr").forEach((fila) => {
      let texto = fila.textContent.toLowerCase();
      fila.style.display = texto.includes(e.target.value.toLowerCase())
        ? ""
        : "none";
    });
  }
});
