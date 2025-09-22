var App = angular.module('app',[]);

App.controller('UsuariosCtrl', function($scope,$http){

    // Objeto para almacenar los datos de un nuevo usuario
    $scope.usuario={};
    
    // Arreglo para almacenar todos los usuarios
    $scope.usuarios=[];
    
    // Función para consultar usuarios desde el backend
    $scope.consultar = function(){               
        $http.post('../api/usuario/consultarUser.php')
        .success(function(data,status,headers,config){
            $scope.usuarios=data;
        }).error(function(data,status,headers,config){
            alert("Error en la Petición");
        });   
    }
    
    // Consultar usuarios al cargar la página
    $scope.consultar();
    
    // Función para guardar un nuevo usuario
$scope.guardar = function () {

    $http.post('../api/usuario/guardarUser.php', $scope.usuario)
        .success(function () {
            $scope.usuario = {};
            $scope.consultar();

            var modal = bootstrap.Modal.getInstance(document.getElementById('myModal'));
            modal.hide();
        })
        .error(function () {
            alert("Error en la Petición");
        });
};

    
    // Objeto para almacenar los datos de un usuario a modificar
    $scope.usuarioMod={};
    
  // Seleccionar usuario y abrir modal de modificar
$scope.seleccionar = function(u){
    $scope.usuarioMod = angular.copy(u); // Copia para no modificar directamente
    var modal = new bootstrap.Modal(document.getElementById('ModalMod'));
    modal.show();
};

// Modificar usuario
$scope.modificar = function(){
    $http.post('../api/usuario/modificarUser.php', $scope.usuarioMod)
        .success(function(data){
            $scope.usuarioMod = {};
            $scope.consultar();
            var modal = bootstrap.Modal.getInstance(document.getElementById('ModalMod'));
            modal.hide();
        })
        .error(function(){
            alert("Error al modificar el usuario");
        });
};

// Eliminar usuario
$scope.eliminar = function(usuario){ 
    if(confirm("¿Deseas eliminar este usuario?")){
        $http.post('../api/usuario/eliminarUser.php', usuario)
            .success(function(data){
                $scope.consultar();
            })
            .error(function(){
                alert("Error al eliminar el usuario");
            });
    }              
};

});

/* BUSCAR USUARIOS POR NOMBRE */
document.addEventListener("keyup", e => {
    if (e.target.id === "buscador") {
        if (e.key === "Escape") e.target.value = "";

        // Itera sobre todas las celdas con la clase "usuario"
        document.querySelectorAll(".nombre").forEach(usuario => {
            // Comprueba si el contenido de la celda coincide con el término de búsqueda
            if (usuario.textContent.toLowerCase().includes(e.target.value.toLowerCase())) {
                usuario.parentElement.style.display = ""; // Muestra la fila
            } else {
                usuario.parentElement.style.display = "none"; // Oculta la fila
            }
        });
    }
});