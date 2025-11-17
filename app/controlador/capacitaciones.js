var App = angular.module("app", []);

App.controller("CapacitacionesCtrl", function ($scope, $http) {

    $scope.usuarios = [];
    $scope.constancias = [];
    $scope.idCapacitacionSeleccionada = null;

    // CONSULTAR CAPACITACIONES
    $scope.consultar = function () {
        $http.post("../api/capacitacion/consultarCapacitaciones.php")
            .success(function (data) {
                $scope.capacitaciones = data;
            })
            .error(function () {
                alert("Error al consultar capacitaciones");
            });
    };

    $scope.consultar();

    // GUARDAR NUEVA
    $scope.guardar = function () {
        $http.post("../api/capacitacion/guardarCapacitacion.php", $scope.nuevo)
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

    // SELECCIONAR PARA EDITAR
    $scope.seleccionar = function (c) {
        $scope.capacitacionMod = angular.copy(c);

        var modal = new bootstrap.Modal(
            document.getElementById("modalEditar")
        );
        modal.show();
    };

    // MODIFICAR
    $scope.modificar = function () {
        $http.post("../api/capacitacion/modificarCapacitacion.php", $scope.capacitacionMod)
            .success(() => {
                $scope.capacitacionMod = {};
                $scope.consultar();

                var modal = bootstrap.Modal.getInstance(
                    document.getElementById("modalEditar")
                );
                modal.hide();
            })
            .error(() => alert("Error al modificar el registro"));
    };

    // ELIMINAR
    $scope.eliminar = function (capacitacion) {
        if (confirm("¿Deseas eliminar este registro?")) {
            $http.post("../api/capacitacion/eliminarCapacitacion.php", capacitacion)
                .success(() => $scope.consultar())
                .error(() => alert("Error al eliminar el registro"));
        }
    };
    // =============== CONSTANCIAS =====================
    $scope.listarConstancias = function (id_cap) {
        $http.post("../api/capacitacion/listarConstancias.php", {
            id_capacitacion: id_cap
        }).then(function (r) {
            $scope.constancias = r.data;

            var modal = new bootstrap.Modal(
                document.getElementById("modalConstancias")
            );
            modal.show();
        });
    };

    $scope.descargarConstancia = function (u) {
        // Aquí pones tu función existente de PDF
    };

});
