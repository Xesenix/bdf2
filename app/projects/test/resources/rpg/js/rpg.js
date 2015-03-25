(function() {
	var rpgApp = angular.module('rpgApp', ['ngRoute', 'ngSanitize']);
	rpgApp.config(function($interpolateProvider, $routeProvider, $httpProvider) {
		$interpolateProvider.startSymbol('[[');
		$interpolateProvider.endSymbol(']]');

		$routeProvider.when('/wstep', {
			controller: 'RpgController',
			templateUrl: 'resources/rpg/part/index.htm'
		}).when('/profil', {
			controller: 'RpgDetailViewController',
			templateUrl: 'resources/rpg/part/profil.htm'
		}).when('/umiejetnosci', {
			controller: 'RpgDetailViewController',
			templateUrl: 'resources/rpg/part/umiejetnosci.htm'
		}).when('/inwentarz', {
			controller: 'RpgInventoryViewController',
			templateUrl: 'resources/rpg/part/inwentarz.htm'
		}).when('/mapa', {
			controller: 'RpgMapController',
			templateUrl: 'resources/rpg/part/mapa.htm'
		}).when('/mapa/:locationId', {
			controller: 'RpgMapController',
			templateUrl: 'resources/rpg/part/mapa.htm'
		}).otherwise({
			redirectTo: '/wstep'
		});
	});

	rpgApp.filter('aggSum', function() {
		return function(propertyName, collection) {
			if (typeof collection == 'undefined')
			{
				return 0;
			}
			
			var i = 0, len = collection.length, suma = 0;
			
			for (; i < len; i++) {
				suma += collection[i][propertyName];
			}
			
			return suma;
		}
	});

	rpgApp.filter('aggCount', function() {
		return function(collection) {
			return collection.length;
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
	
	rpgApp.factory('skillsFactory', ['$q', '$interpolate', function($q, $interpolate) {
		var skillPrototype = {
			progressTemplate: 'resources/rpg/part/skill/progress.htm',
			lvl: function() {
				return Math.floor(Math.sqrt(this.pointsUsed));
			},
			lvlRequired: function(lvl) {
				if (typeof lvl == 'undefined')
				{
					lvl = this.lvl();
				}
				return Math.pow(lvl, 2);
			},
			findChance: function(lvl) {
				if (typeof lvl == 'undefined')
				{
					lvl = this.lvl();
				}
				return 50 * lvl / (lvl + 1);
			},
			findTryCount: function(lvl) {
				if (typeof lvl == 'undefined')
				{
					lvl = this.lvl();
				}
				return 5 * lvl / (lvl + 1);
			}
		};
		
		var skills = {
			search: angular.extend(angular.copy(skillPrototype), {
				key: 'search',
				name: 'Przeszukiwanie',
				descriptionTemplate: 'resources/rpg/part/skill/search.htm'
			}),
			diplomacy: angular.extend(angular.copy(skillPrototype), {
				key: 'diplomacy',
				name: 'Dyplomacja',
				description: ''
			}),
			theft: angular.extend(angular.copy(skillPrototype), {
				key: 'theft',
				name: 'Kradzież',
				description: ''
			})
		}
		
		return $q(function(resolve, reject) {
			resolve(skills);
		});
	}]);
	
	rpgApp.factory('profilFactory', ['$q', '$filter', 'skillsFactory', function($q, $filter, skillsFactory) {
		var avaibleSkills = [];
		
		skillsFactory.then(function(skills) {
			profil.skills = skills;
			
			angular.forEach(profil.skills, function(value, key) {
				profil.skills[key].pointsUsed = 0;
			});
		});
		
		var profil = {
			skillsPoints: 100,
			skills: {},
			addSkillPoint: function(skillKey, multiply) {
				if (typeof multiply == 'undefinde')
				{
					multiply = 1;
				}
				
				var diff = Math.min(multiply, profil.skillsPoints);
				
				profil.skills[skillKey].pointsUsed += diff;
				profil.skillsPoints -= diff;
				
			},
			removeSkillPoint: function(skillKey, multiply) {
				if (typeof multiply == 'undefinde')
				{
					multiply = 1;
				}
				
				var diff = Math.min(multiply, profil.skills[skillKey].pointsUsed);
				
				profil.skills[skillKey].pointsUsed -= diff;
				profil.skillsPoints += diff;
				
			},
			avaibleSkillPoints: function() {
				return profil.skillsPoints - $filter('aggSum')('pointsUsed', profil.skills);
			}
		};
		
		return $q(function(resolve, reject) {
			resolve(profil);
		});
	}]);

	rpgApp.controller('RpgController', function($rootScope, $route, $location, $filter, skillsFactory, profilFactory) {
		$rootScope.clickModifier = function($event) {
			if ($event.ctrlKey)
			{
				return 5;
			}
			
			if ($event.shiftKey)
			{
				return 50;
			}
			
			return 1;
		}
		
		$rootScope.$filter = $filter;
		$rootScope.historia = {};
		
		profilFactory.then(function(profil) {
			$rootScope.profil = profil;
		});
		
		$rootScope.tabs = [{
			label: 'Wstęp',
			route: function() {
				return '/wstep';
			},
		}, {
			label: 'Profil',
			route: function() {
				return '/profil';
			},
		}, {
			label: 'Umiejętności',
			route: function() {
				return '/umiejetnosci';
			},
			badge: 'profil.avaibleSkillPoints()'
		}, {
			label: 'Inwentarz',
			route: function() {
				return '/inwentarz';
			}
		}, {
			label: 'Mapa',
			route: function() {
				return $rootScope.map.location.route;
			}
		}];
		
		$rootScope.activeRoute = function(path) {
			return $location.path().substr(0, path.length) == path;
		}
		
		$rootScope.changeLocation = function(locationId) {
			var newLocation = $filter('filter')($rootScope.map.locations, {'locationId': locationId});
			
			if (newLocation.length > 0)
			{
				$rootScope.map.location = newLocation[0];
			}
		}

		$rootScope.label = $route.current;
		$rootScope.map = {
			location: null,
			locations: [{
				locationId: 'dom',
				miejscownik: 'w domu',
				route: '/mapa/dom',
				moveToLabel: 'Wróć do domu'
			}, {
				locationId: 'sklep',
				miejscownik: 'w sklepie',
				route: '/mapa/sklep',
				moveToLabel: 'Idź do sklepu'
			}, {
				locationId: 'warsztat',
				miejscownik: 'w warsztacie',
				route: '/mapa/warsztat',
				moveToLabel: 'Idź do warsztatu'
			}, {
				locationId: 'rynek',
				miejscownik: 'na rynku',
				route: '/mapa/rynek',
				moveToLabel: 'Przejdź na rynek'
			}, {
				locationId: 'dzicz',
				miejscownik: 'poza miastem',
				route: '/mapa/dzicz',
				moveToLabel: 'Wyjedź za miasto'
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