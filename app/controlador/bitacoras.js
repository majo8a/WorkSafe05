var App = angular.module("app", []);

App.controller("BitacoraCtrl", function ($scope, $http) {
  // Objeto para almacenar los datos de un nuevo registro
  $scope.bitacora = {};

  // Arreglo para almacenar todos los registros de la bitácora
  $scope.bitacoras = [];

  // Función para consultar registros desde el backend
  $scope.consultar = function () {
    $http
      .post("../api/bitacora/consultarBitacoras.php")
      .success(function (data) {
        $scope.bitacoras = data;
      })
      .error(function () {
        alert("Error en la petición de bitácora");
      });
  };

  // Consultar bitácora al cargar la página
  $scope.consultar();

  // Función para guardar un nuevo registro
  $scope.guardar = function () {
    $http
      .post("../api/bitacora/guardarBitacora.php", $scope.bitacora)
      .success(function () {
        $scope.bitacora = {};
        $scope.consultar();

        var modal = bootstrap.Modal.getInstance(
          document.getElementById("myModal")
        );
        modal.hide();
      })
      .error(function () {
        alert("Error al guardar el registro");
      });
  };

  // Objeto para almacenar los datos de un registro a modificar
  $scope.bitacoraMod = {};

  // Seleccionar registro y abrir modal de modificar
  $scope.seleccionar = function (b) {
    $scope.bitacoraMod = angular.copy(b); // Copia para no modificar directamente
    var modal = new bootstrap.Modal(document.getElementById("ModalMod"));
    modal.show();
  };

  // Modificar registro
  $scope.modificar = function () {
    $http
      .post("../api/bitacora/modificarBitacora.php", $scope.bitacoraMod)
      .success(function () {
        $scope.bitacoraMod = {};
        $scope.consultar();
        var modal = bootstrap.Modal.getInstance(
          document.getElementById("ModalMod")
        );
        modal.hide();
      })
      .error(function () {
        alert("Error al modificar el registro");
      });
  };

  // Eliminar registro
  $scope.eliminar = function (bitacora) {
    if (confirm("¿Deseas eliminar este registro?")) {
      $http
        .post("../api/bitacora/eliminarBitacora.php", bitacora)
        .success(function () {
          $scope.consultar();
        })
        .error(function () {
          alert("Error al eliminar el registro");
        });
    }
  };
});

/* BUSCAR REGISTROS POR USUARIO, ACCION O DESCRIPCION */
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
