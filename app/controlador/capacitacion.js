var app = angular.module("app", []);

app.controller("CapacitacionesCtrl", function($scope, $http) {

    $scope.capacitaciones = [];
    $scope.modalData = {}; 
    $scope.usuario = {};

    // Cargar usuario de sesión
    $http.get("../api/usuario/getUsuarioSesion.php")
    .then(function(response){
        $scope.usuario = response.data;
    });

    $scope.cargarCapacitaciones = function () {
        $http.get("../api/capacitacion/consultarCapacitaciones.php")
        .then(function (response) {
            $scope.capacitaciones = response.data;
        }, function (error) {
            console.error("Error cargando capacitaciones:", error);
        });
    };

    $scope.cargarCapacitaciones();

    // Abrir modal
    $scope.abrirModal = function(c) {
        if (c.confirmado == 1) return;
        $scope.modalData = c;

        var modal = new bootstrap.Modal(document.getElementById("modalConfirmar"));
        modal.show();
    };

    $scope.confirmarAsistencia = function() {

        let data = {
            id_usuario: $scope.usuario.id_usuario,  // ← AHORA SÍ TIENE VALOR REAL
            id_capacitacion: $scope.modalData.id_capacitacion,
            tipo_confirmacion: "Usuario",
            fecha_confirmacion: new Date().toISOString().slice(0,19).replace("T"," "),
            asistio: 1
        };

        $http.post("../api/confirmacion/guardarConfirmacion.php", data)
        .then(function(response) {

            if (response.data.status === "success") {

                $scope.modalData.confirmado = 1;

                var modal = bootstrap.Modal.getInstance(document.getElementById("modalConfirmar"));
                modal.hide();

            } else {
                alert(response.data.message);
            }

        }, function(error) {
            console.error("Error al confirmar asistencia:", error);
            alert("Error al confirmar la asistencia.");
        });
    };
});
