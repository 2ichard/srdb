'use strict';

var srdbSegTableApp = angular.module('srdbSegTableApp', []);

srdbSegTableApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbSegTableApp.controller('SegTableCtrl', ['$scope', '$http', '$window', '$log', 

	function($scope, $http, $window, $log) {
	
		$scope.name = 'SegTableCtrl';
		$scope.types = [1, 2, 3, 4, 5, 6];
		
		$scope.inedit = [];
		$scope.inedit.id = 0;
		
		$scope.segs = [];
		
		$scope.edit = function(s, type) {
			$scope.inedit.id = s.id;
			$scope.inedit.type = type;
			$scope.inedit.sn = s.sn;
		}
		
		$scope.save = function() {
			if (!$scope.inedit.type) {
				$window.alert('Please select segment type.');
				return;
			}
			if (!$scope.inedit.sn) {
				$window.alert('Please enter serial number.');
				return;
			}
			
			$scope.url = "../lib/SRDb.php?dbcmnd=addseg&id=" + $scope.inedit.id +
					"&type=" + $scope.inedit.type + "&sn=" + $scope.inedit.sn;
			$log.log($scope.url);
			$http.get($scope.url).success(
				function(data) {
					$scope.update();
					$scope.clear();
				}
			);
		}
		
		$scope.clear = function() {
			$scope.inedit.id = 0;
			$scope.inedit.type = null;
			$scope.inedit.sn = null;
		}
		
		$scope.update = function() {
			var url = "";
			$log.log("update()");
			angular.forEach($scope.types, function(type, index) {
				url = '../lib/SRDb.php?dbcmnd=getsegsjson&type=' + type;
				$log.log('url=' + url + ' type=' + type + ' index=' + index);
				$http.get(url).success(
					function(data) {
						$scope.segs[type] = data;
					}
				);
			});
		}
		
		$scope.delete = function(id) {
			$scope.url = '../lib/SRDb.php?dbcmnd=deleteseg&id=' + id;
			$http.get($scope.url).success(
				function(data) {
					$log.log($scope.url);
					$scope.update();
				}
			);
		}
				
		$scope.update();
	}
]);
