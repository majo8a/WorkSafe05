var App = angular.module("app", []);

App.controller("ConfirmacionesCtrl", function ($scope, $http) {

    $scope.confirmaciones = [];
    $scope.nuevo = {};
    $scope.confirmacionMod = {};

    // ===============================
    // CONSULTAR CONFIRMACIONES
    // ===============================
    $scope.consultar = function () {
        $http.post("../api/confirmacion/consultarConfirmaciones.php")
            .success(function (data) {
                $scope.confirmaciones = data;
            })
            .error(function () {
                alert("Error al consultar confirmaciones");
            });
    };

    $scope.consultar();

    // ===============================
    // GUARDAR NUEVA CONFIRMACIÓN
    // ===============================
    $scope.guardar = function () {
        $http.post("../api/confirmacion/guardarConfirmacion.php", $scope.nuevo)
            .success(() => {
                $scope.nuevo = {};
                $scope.consultar();

                var modal = bootstrap.Modal.getInstance(
                    document.getElementById("modalAgregar")
                );
                modal.hide();
            })
            .error(() => alert("Error al guardar el registro"));
    };

    // ===============================
    // SELECCIONAR PARA MODIFICAR
    // ===============================
   $scope.seleccionar = function (c) {

    let f = new Date(c.fecha_confirmacion);

    c.fecha_confirmacion = f.toISOString().slice(0,16); 
    // Esto da: 2025-11-17T13:31

    $scope.confirmacionMod = angular.copy(c);

    var modal = new bootstrap.Modal(
        document.getElementById("modalEditar")
    );
    modal.show();
};


    // ===============================
    // MODIFICAR CONFIRMACIÓN
    // ===============================
    $scope.modificar = function () {
        $http.post("../api/confirmacion/modificarConfirmacion.php", $scope.confirmacionMod)
            .success(() => {
                $scope.confirmacionMod = {};
                $scope.consultar();

                var modal = bootstrap.Modal.getInstance(
                    document.getElementById("modalEditar")
                );
                modal.hide();
            })
            .error(() => alert("Error al modificar el registro"));
    };

    // ===============================
    // ELIMINAR CONFIRMACIÓN
    // ===============================
    $scope.eliminar = function (confirmacion) {
        if (confirm("¿Deseas eliminar este registro?")) {
            $http.post("../api/confirmacion/eliminarConfirmacion.php", confirmacion)
                .success(() => $scope.consultar())
                .error(() => alert("Error al eliminar el registro"));
        }
    };

});
