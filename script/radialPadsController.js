'use strict';

var srdbRadialPadsApp = angular.module('srdbRadialPadsApp', ['ngCookies']);

srdbRadialPadsApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbRadialPadsApp.controller('RadialPadsCtrl', ['$scope', '$http', '$window', '$log', '$timeout',

	function($scope, $http, $window, $log, $timeout) {
	
		$scope.name = "RadialPadsCtrl";
		$scope.seg = "unk";
		$scope.inedit = [];
		$scope.insert = [];
		$scope.lotList = [];
		$scope.enableAll = true;
		$scope.lotAll = "";
		$scope.orderProp = "pos.lot";
		
		$scope.positions = ["R1", "R2", "R3", "R4", "R5", "R6"];
		
		var pos;
		for (pos in $scope.positions) {
			$scope.inedit[$scope.positions[pos]] = false;		
			$scope.insert[$scope.positions[pos]] = {"lot": "no value yet", "timestamp": ""};
		}
		
		$scope.update = function() {
			if ($scope.seg.length < 3 || $scope.seg == "unknown") return;
			var url = '../lib/SRDb.php?dbcmnd=getInstalledLotNosJSON&type=radial_pad&seg=' + $scope.seg;
			$log.log(url);
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
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
			var url = '../lib/SRDb.php?dbcmnd=getLotsJSON&type=radial_pads&activeOnly=true';
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
			var url = "../lib/SRDb.php?dbcmnd=updateInstalledLotNo&type=radial_pad&seg=" + $scope.seg +
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
			if ($scope.seg.length < 3 || $scope.seg == "unknown") {
				$window.alert("Cannont apply because segment number has not been specified.");
				return;
			}
			
			if ($scope.lotAll.length < 1) {
				$window.alert("Please select lot number.");
				return;
			}
			for (var i in $scope.positions) {
				var url = "../lib/SRDb.php?dbcmnd=updateInstalledLotNo&type=radial_pad&seg=" + $scope.seg +
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
