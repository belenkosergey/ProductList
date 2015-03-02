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
        });
        $routeProvider.when('/product', {
            templateUrl: path + 'product.html',
            controller: 'productController'
        });
        $routeProvider.when('/product/:id', {
            templateUrl: path + 'product.html',
            controller: 'productController'
        });
        $routeProvider.otherwise({
            redirectTo: '/products'
        });
    }]);

app.controller('mainController', function mainController($scope, $http, $location, $route){
        var products = [];
        $http.get('api/products').success(function(data){
            angular.forEach(data, function(value, key){
               products.push(value); 
            });
            $scope.sortparam = 'title';
            $scope.products = products;
        });
        
        $scope.remove = function(id){
          if(confirm('Are you sure you want to delete this product?'))
              {
                  $http.delete('api/product/' + id).success(function(){
                            $route.reload();
                        });
              }
        };
});

app.controller('productController', function productController($scope, $http, $location, $templateCache, $routeParams){
        $scope.product = null;
        
        $scope.$on('$routeChangeStart', function(event, next, current) {
            if (typeof(current) !== 'undefined'){
                $templateCache.remove(next.templateUrl);
            }
        });
        
        $scope.$on("$routeChangeSuccess", function () {
            var id = $routeParams["id"]
            if(typeof(id)!=='undefined'){
             
                $http.get('api/product/' + id).
                success(function(data) {
                    $scope.product = data;
                });
            }
        });
        
        $scope.save = function(product, productForm){
          if(productForm.$valid)
              {
                  if(typeof(product.id)!=='undefined'){
                        $http.put('api/product/' + product.id, product).success(function(){
                            $location.path('products');
                        });
                  }
                  else{
                        $http.post('api/product', product).success(function(){
                            $location.path('products');
                        });
                  }
              }
        };
        
});