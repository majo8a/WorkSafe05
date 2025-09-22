var App = angular.module('app',[]);

App.controller('adminCtrl', function($scope,$http){


    $scope.administracion={};

    $scope.consultar = function(){               
        $http.post('../api/historial-cambios/consultarHistorial.php')
        .success(function(data,status,headers,config){
            $scope.administracion=data;
        }).error(function(data,status,headers,config){
            alert("Error en la Petición");
        });   
    }
 
    $scope.consultar();
         
});