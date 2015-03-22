(function() {
	var rpgApp = angular.module('rpgApp', ['ngRoute']);

	rpgApp.config(function($interpolateProvider, $routeProvider, $httpProvider) {
		$interpolateProvider.startSymbol('[[');
		$interpolateProvider.endSymbol(']]');

		$routeProvider.when('/wstep', {
			controller : 'RpgController',
			templateUrl : 'resources/rpg/part/index.htm'
		}).when('/profil', {
			controller : 'RpgDetailViewController',
			templateUrl : 'resources/rpg/part/profil.htm'
		}).when('/umiejetnosci', {
			controller : 'RpgDetailViewController',
			templateUrl : 'resources/rpg/part/umiejetnosci.htm'
		}).when('/inwentarz', {
			controller : 'RpgInventoryViewController',
			templateUrl : 'resources/rpg/part/inwentarz.htm'
		}).when('/mapa', {
			controller : 'RpgMapController',
			templateUrl : 'resources/rpg/part/mapa.htm'
		}).when('/mapa/:locationId', {
			controller : 'RpgMapController',
			templateUrl : 'resources/rpg/part/mapa.htm'
		}).otherwise({
			redirectTo : '/wstep'
		});
	});

	rpgApp.filter('getByProperty', function() {
		return function(propertyName, propertyValue, collection) {
			var i = 0, len = collection.length;
			for (; i < len; i++) {
				if (collection[i][propertyName] == propertyValue) {
					return collection[i];
				}
			}
			return null;
		}
	});
	
	rpgApp.factory('inventoryFactory', ['$http', function($http){
		var url = 'resources/rpg/data/inventory.csv';
		var items = $http.get(url).then(function(response){
			items = Papa.parse(response.data, {
				worker: false,
				header: true
			});
			
			return items;
		});
		
		return items;
	}]);

	rpgApp.controller('RpgController', function($rootScope, $route, $location, $filter) {

		$rootScope.tabs = [{
			label : 'Wstęp',
			route : function() {
				return '/wstep';
			},
		}, {
			label : 'Profil',
			route : function() {
				return '/profil';
			},
		}, {
			label : 'Umiejętności',
			route : function() {
				return '/umiejetnosci';
			},
			badge : '+5'
		}, {
			label : 'Inwentarz',
			route : function() {
				return '/inwentarz';
			},
			badge : '25/50'
		}, {
			label : 'Mapa',
			route : function() {
				return $rootScope.map.location.route;
			},
			badge : 'nowe'
		}];

		$rootScope.activeRoute = function(path) {
			return $location.path().substr(0, path.length) == path;
		}
		
		$rootScope.changeLocation = function(locationId) {
			var newLocation = $filter('getByProperty')('locationId', locationId, $rootScope.map.locations);
			
			if (newLocation)
			{
				$rootScope.map.location = newLocation;
			}
		}

		$rootScope.label = $route.current;
		$rootScope.map = {
			location : null,
			locations : [{
				locationId : 'dom',
				miejscownik: 'w domu',
				route : '/mapa/dom',
				moveToLabel : 'Wróć do domu'
			}, {
				locationId : 'sklep',
				miejscownik: 'w sklepie',
				route : '/mapa/sklep',
				moveToLabel : 'Idź do sklepu'
			}, {
				locationId : 'warsztat',
				miejscownik: 'w warsztacie',
				route : '/mapa/warsztat',
				moveToLabel : 'Idź do warsztatu'
			}, {
				locationId : 'rynek',
				miejscownik: 'na rynku',
				route : '/mapa/rynek',
				moveToLabel : 'Przejdź na rynek'
			}, {
				locationId : 'dzicz',
				miejscownik: 'poza miastem',
				route : '/mapa/dzicz',
				moveToLabel : 'Wyjedź za miasto'
			}]
		};
		
		$rootScope.changeLocation($rootScope.map.locationId || 'dom');
	});

	rpgApp.controller('RpgDetailViewController', function($scope, $rootScope, $location) {
		var path = '#' + $location.path();

		$rootScope.label = path;
	});

	rpgApp.controller('RpgMapController', function($rootScope, $location, $routeParams) {
		var path = '#' + $location.path();
		
		$rootScope.label = path;
		
		$rootScope.changeLocation($routeParams.locationId || 'dom');
	});

	rpgApp.controller('RpgInventoryViewController', function($rootScope, inventoryFactory) {
		$rootScope.label = 'open';
		inventoryFactory.then(function(result) {
			$rootScope.label = result;
			$rootScope.inventory = result;
		}, function() {
			// reject
			$rootScope.label = 'rejected';
		}, function() {
			// progress
			$rootScope.label = 'loading...';
		});
	});
})();