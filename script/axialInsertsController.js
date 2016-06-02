'use strict';

var srdbAxialInsertsApp = angular.module('srdbAxialInsertsApp', ['ngCookies']);

srdbAxialInsertsApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbAxialInsertsApp.controller('AxialInsertsCtrl', ['$scope', '$http', '$window', '$log', '$timeout',

	function($scope, $http, $window, $log, $timeout) {
	
		$scope.name = "AxialInsertsCtrl";
		$scope.seg = "unk";
		$scope.inedit = [];
		$scope.insert = [];
		$scope.lotList = [];
		$scope.enableAll = true;
		$scope.lotAll = "";
		$scope.orderProp = "pos";
		
		$scope.positions = ["S1", "S2", "S3", "S4", "S5", "S6", "S7", "S8", "S9", "S10", "S11", "S12",
			"W1-1", "W1-2", "W1-3", "W1-4", "W1-5", "W1-6", "W2-1", "W2-2", "W2-3", "W2-4", "W2-5", "W2-6",
			"W3-1", "W3-2", "W3-3", "W3-4", "W3-5", "W3-6", "W4-1", "W4-2", "W4-3", "W4-4", "W4-5", "W4-6",
			"W5-1", "W5-2", "W5-3", "W5-4", "W5-5", "W5-6", "W6-1", "W6-2", "W6-3", "W6-4", "W6-5", "W6-6",
			];
		
		var pos;
		for (pos in $scope.positions) {
			$scope.inedit[$scope.positions[pos]] = false;		
			$scope.insert[$scope.positions[pos]] = {"lot": "no value yet", "timestamp": ""};
		}
		
		$scope.update = function() {
			var url = '../lib/SRDb.php?dbcmnd=getInstalledLotNosJSON&type=axial_insert&seg=' + $scope.seg;
			//$log.log(url);
			$http.get(url).success(
				function(data) {
					if (data.length > 0)
						$scope.enableAll = false;
					for (var i = 0; i < data.length; i++) {
						$scope.insert[data[i].pos].lot = data[i].lot;
						$scope.insert[data[i].pos].timestamp = data[i].timestamp;
					}
				}
			);
		}
		
		$scope.getLotList = function() {
			var url = '../lib/SRDb.php?dbcmnd=getLotsJSON&type=axial_inserts&activeOnly=true';
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					for (var i = 0; i < data.length; i++) {
						$scope.lotList[i] = data[i].lot;
					}
				}
			);
		}
				
		$scope.edit = function(pos) {
			
			if (!$scope.seg || $scope.seg.length < 3 || $scope.seg.indexOf("unk") >= 0) {
				$window.alert("There is no segment S/N currently selected.");
				return;
			}
			
			$scope.inedit[pos] = true;
		}
		
		$scope.cancel = function(pos) {
			$scope.inedit[pos] = false;
		}
		
		$scope.save = function(pos) {
			var url = "../lib/SRDb.php?dbcmnd=updateInstalledLotNo&type=axial_insert&seg=" + $scope.seg +
					"&pos=" + pos + "&lot=" + $scope.insert[pos].lot;
			url = url.replace(/[\s]/g, '%20');
			url = url.replace(/[#]/g, '%23');
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					$scope.inedit[pos] = false;
					$scope.update();				
				}
			);
		}
		
		$scope.saveAll = function() {
			$log.log($scope.lotAll.length);
			if ($scope.lotAll.length < 1) {
				$window.alert("Please select lot number.");
				return;
			}
			for (var i in $scope.positions) {
				var url = "../lib/SRDb.php?dbcmnd=updateInstalledLotNo&type=axial_insert&seg=" + $scope.seg +
					"&pos=" + $scope.positions[i] + "&lot=" + $scope.lotAll;
				url = url.replace(/[\s]/g, '%20');
				url = url.replace(/[#]/g, '%23');
				$log.log(url);
				$http.get(url).success(
					function(data) {
						if (data.indexOf("error") >= 0) {
							$window.alert(data)
							return;
						}	
						$scope.inedit[pos] = false;
					}
				);
			}
			$scope.update();				
		}
		
		$scope.getLotList();
		$timeout($scope.update, 1000);
	}
]);
