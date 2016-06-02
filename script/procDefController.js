'use strict';

var srdbProcDefApp = angular.module('srdbProcDefApp', []);

srdbProcDefApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbProcDefApp.controller('ProcListCtrl', ['$scope', '$http', '$window', '$log', 

	function($scope, $http, $window, $log) {
	
		$scope.name = "ProcListCtrl";
		$scope.orderProp = 'num';
		$scope.inedit = [];
		$scope.procs = [];
		
		$scope.edit = function(id) {
			$scope.inedit[id] = true;
		}
		
		$scope.createNew = function() {
			for (var i = 0; i < $scope.procs.length; i++) {
				if ($scope.inedit[$scope.procs[i].id]) {
					$window.alert("Finsih editing first.");
					return;
				}
			}
			$scope.procs.push({"id": 0, "type": "", "order": 0, "num": "", 
				"title": "", "filename": ""});
			$scope.inedit[0] = true;
		}
		
		$scope.unedit = function(id) {
			$scope.inedit[id] = false;
			if (id == 0)
				$scope.procs.pop();
		}
		
		$scope.save = function(id) {
			var proc = [];
			var i;
			for (i = 0; i < $scope.procs.length; i++) {
				if ($scope.procs[i].id == id) {
					proc = $scope.procs[i];
					break;
				}
			}
			
			if (i == $scope.procs.length) {
				$log.log("ERROR: invalid id");
				return;
			}
							
			if (!proc.type) {
				$window.alert("Please enter procedure type.");
				return;
			}
			if (!proc.order) {
				$window.alert("Please enter procedure order.");
				return;
			}
			if (!proc.num) {
				$window.alert("Please enter procedure number.");
				return;
			}
			if (!proc.title) {
				$window.alert("Please enter procedure title.");
				return;
			}
			if (!proc.filename) {
				$window.alert("Please enter procedure filename.");
				return;
			}
			
			$scope.url = "../lib/SRDb.php?dbcmnd=addProc&id=" + proc.id + "&type=" + proc.type + 
					"&order=" + proc.order + "&num=" + proc.num + "&title=" + proc.title +
					"&fn=" + proc.filename;
			$scope.url = $scope.url.replace(/[\s]/g, '%20');
			$scope.url = $scope.url.replace(/[#]/g, '%23');
			$log.log($scope.url);
			$http.get($scope.url).success(
				function(data) {
					if (proc.id == 0) $scope.procs.pop();
					$scope.inedit[proc.id] = false;
					$scope.update();
				}
			);
		}
		
		$scope.delete = function(id) {
			var url = "../lib/SRDb.php?dbcmnd=deleteProc&id=" + id;
			$log.log(url);
			$http.get(url).success(
				function(data) {
					$scope.update();
				}
			);
		}
				
		$scope.update = function() {
			$http.get('../lib/SRDb.php?dbcmnd=getProcsJSON&type=all').success(
				function(data) {
					$scope.procs = data;
					for (var $proc in $scope.procs)
						$scope.inedit[$proc.id] = false;
				}
			);
		}
		
		$scope.openProcedure = function(type, num) {
			$window.openProcedure(type, num);
		}
		
		$scope.update();
	}
]);
