'use strict';

var srdbTechTableApp = angular.module('srdbTechTableApp', []);

srdbTechTableApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbTechTableApp.controller('TechTableCtrl', ['$scope', '$http', '$window', '$log', 

	function($scope, $http, $window, $log) {
	
		$scope.name = 'TechTableCtrl';
		
		$scope.newTech = "";
		
		$scope.techs = [];
		

		$scope.add = function() {
			if ($scope.newTech =="") {
				$window.alert('Please enter a tech name.');
				return;
			}
			if ($scope.newTech.length < 3) {
				$window.alert('Please enter a longer tech name.\nMinimum length is 3.');
				return;
			}
			
			$scope.url = "../lib/SRDb.php?dbcmnd=addTech&name=" + $scope.newTech;
			$log.log($scope.url);
			$http.get($scope.url).success(
				function(data) {
					$scope.update();
					$scope.newTech = "";
				}
			);
		}

		$scope.update = function() {
			var url = '../lib/SRDb.php?dbcmnd=getTechsJSON';
			$http.get(url).success(function(data) {
				$scope.techs = data;
			});
		}
		
		$scope.delete = function(name) {
			if (confirm("Are you sure you want to delete " + name + "?") != true)
				return;
			$scope.url = '../lib/SRDb.php?dbcmnd=deleteTech&name=' + name;
			$http.get($scope.url).success(
				function(data) {
					$scope.update();
				}
			);
		}
				
		$scope.update();
	}
]);
