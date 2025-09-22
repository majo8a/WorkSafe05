var App = angular.module('app', []);

App.controller('ReportesCtrl', function($scope, $http){
    $scope.resultado = {};
    $scope.resultadoMod = {};
    $scope.resultados = [];

    // Consultar resultados
    $scope.consultar = function(){
        $http.post('../api/resultado/consultarResultado.php')
        .then(function(response){
            $scope.resultados = response.data;
        }, function(error){
            alert("Error al consultar");
        });
    };
    $scope.consultar();

    // Guardar resultado
    $scope.guardar = function(){
        $http.post('../api/resultado/guardarResultado.php', $scope.resultado)
        .then(function(response){
            if(response.data.status === 'success'){
                $scope.resultado = {};
                $scope.consultar();
                var modal = bootstrap.Modal.getInstance(document.getElementById('myModal'));
                modal.hide();
            } else {
                alert(response.data.message);
            }
        }, function(){
            alert("Error en la petición");
        });
    };

    // Seleccionar para modificar
    $scope.seleccionar = function(r){
        $scope.resultadoMod = angular.copy(r);
        var modal = new bootstrap.Modal(document.getElementById('ModalMod'));
        modal.show();
    };

    // Modificar
    $scope.modificar = function(){
        $http.post('../api/resultado/modificarResultado.php', $scope.resultadoMod)
        .then(function(response){
            $scope.resultadoMod = {};
            $scope.consultar();
            var modal = bootstrap.Modal.getInstance(document.getElementById('ModalMod'));
            modal.hide();
        }, function(){
            alert("Error al modificar");
        });
    };

    // Eliminar
    $scope.eliminar = function(resultado){
        if(confirm("¿Deseas eliminar este resultado?")){
            $http.post('../api/resultado/eliminarResultado.php', resultado)
            .then(function(){
                $scope.consultar();
            }, function(){
                alert("Error al eliminar");
            });
        }
    };
});

/* BUSCAR RESULTADOS POR CATEGORIA */
document.addEventListener("keyup", e => {
    if (e.target.id === "buscador") {
        if (e.key === "Escape") e.target.value = "";

        // Itera sobre todas las celdas con la clase "usuario"
        document.querySelectorAll(".categoria").forEach(usuario => {
            // Comprueba si el contenido de la celda coincide con el término de búsqueda
            if (usuario.textContent.toLowerCase().includes(e.target.value.toLowerCase())) {
                usuario.parentElement.style.display = ""; // Muestra la fila
            } else {
                usuario.parentElement.style.display = "none"; // Oculta la fila
            }
        });
    }
});