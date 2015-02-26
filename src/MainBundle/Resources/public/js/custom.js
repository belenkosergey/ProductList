var origin = document.location.origin;
var folder = document.location.pathname.split('/')[1];

var path = origin + '/' + folder + "/web/bundles/main/partials/"

var app = angular.module('productListApp',[
    'ngRoute'
]).
    config(['$routeProvider', function($routeProvider){
        $routeProvider.when('/products', {
            templateUrl: path + 'list.html',
            controller: 'mainController'
        }).otherwise({
            redirectTo: '/products'
        });
    }]);

app.controller('mainController', ['$scope', '$http', function($scope, $http){
        var products = [];
        $http.get('api/products').success(function(data){
            angular.forEach(data, function(value, key){
               products.push(value); 
            });
            $scope.sortparam = 'title';
            $scope.products = products;
        });
}])