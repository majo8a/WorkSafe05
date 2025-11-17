var App = angular.module("app", []);

App.controller("UsuariosCtrl", function ($scope, $http) {
  // =======================================
  // VARIABLES
  // =======================================
  $scope.usuario = {}; // Para guardar
  $scope.usuarioMod = {}; // Para modificar
  $scope.usuarioHistorial = {}; // Para mostrar info en el modal historial
  $scope.usuarios = []; // Lista de usuarios
  $scope.historial = []; // Historial de evaluaciones del usuario

  // =======================================
  // CONSULTAR USUARIOS
  // =======================================
  $scope.consultar = function () {
    $http
      .post("../api/usuario/consultarUser.php")
      .success(function (data) {
        $scope.usuarios = data;
      })
      .error(function () {
        alert("Error en la Petición");
      });
  };

  // Ejecutar al cargar
  $scope.consultar();

  // =======================================
  // GUARDAR USUARIO
  // =======================================
  $scope.guardar = function () {
    $http
      .post("../api/usuario/guardarUser.php", $scope.usuario)
      .success(function () {
        $scope.usuario = {};
        $scope.consultar();

        var modal = bootstrap.Modal.getInstance(
          document.getElementById("myModal")
        );
        modal.hide();
      })
      .error(function () {
        alert("Error en la Petición");
      });
  };

  // =======================================
  // SELECCIONAR USUARIO PARA MODIFICAR
  // =======================================
  $scope.seleccionar = function (u) {
    $scope.usuarioMod = angular.copy(u);
    var modal = new bootstrap.Modal(document.getElementById("ModalMod"));
    modal.show();
  };

  // =======================================
  // MODIFICAR USUARIO
  // =======================================
  $scope.modificar = function () {
    $http
      .post("../api/usuario/modificarUser.php", $scope.usuarioMod)
      .success(function () {
        $scope.usuarioMod = {};
        $scope.consultar();

        var modal = bootstrap.Modal.getInstance(
          document.getElementById("ModalMod")
        );
        modal.hide();
      })
      .error(function () {
        alert("Error al modificar el usuario");
      });
  };

  // =======================================
  // ELIMINAR USUARIO
  // =======================================
  $scope.eliminar = function (usuario) {
    if (confirm("¿Deseas eliminar este usuario?")) {
      $http
        .post("../api/usuario/eliminarUser.php", usuario)
        .success(function () {
          $scope.consultar();
        })
        .error(function () {
          alert("Error al eliminar el usuario");
        });
    }
  };

  // =======================================
  // 🚀 NUEVO: VER HISTORIAL DE EVALUACIONES
  // =======================================
  $scope.verHistorial = function (u) {
    // Guardar datos del usuario para el modal
    $scope.usuarioHistorial = u;

    // Llamar API del historial
    $http
      .get("../api/usuario/obtenerHistorial.php?id_usuario=" + u.id_usuario)
      .then(function (resp) {
        $scope.historial = resp.data;

        // Mostrar el modal
        var modal = new bootstrap.Modal(
          document.getElementById("ModalHistorial")
        );
        modal.show();
      })
      .catch(function () {
        alert("Error al obtener el historial del usuario");
      });
  };
});

// =======================================
// BUSCADOR DE USUARIOS POR NOMBRE
// =======================================
document.addEventListener("keyup", (e) => {
  if (e.target.id === "buscador") {
    if (e.key === "Escape") e.target.value = "";

    document.querySelectorAll(".nombre").forEach((usuario) => {
      if (
        usuario.textContent.toLowerCase().includes(e.target.value.toLowerCase())
      ) {
        usuario.parentElement.style.display = "";
      } else {
        usuario.parentElement.style.display = "none";
      }
    });
  }
});
