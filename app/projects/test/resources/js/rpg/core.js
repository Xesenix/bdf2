(function(){
	var rpgApp = angular.module('rpgApp', []);
	
	rpgApp.config(function($interpolateProvider) {
		$interpolateProvider.startSymbol('[[');
		$interpolateProvider.endSymbol(']]');
	});
	
	rpgApp.controller('RpgController', function() {
		this.label = "Test Angular JS.";
	});
})();