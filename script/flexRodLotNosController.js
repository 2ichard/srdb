'use strict';

var srdbFlexRodLotNosApp = angular.module('srdbFlexRodLotNosApp', ['ngCookies']);

srdbFlexRodLotNosApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbFlexRodLotNosApp.controller('FlexRodLotNosCtrl', ['$scope', '$http', '$window', '$log', '$timeout', '$cookies',

	function($scope, $http, $window, $log, $timeout, $cookies) {
	
		$scope.name = "FlexRodLotNosCtrl";
		$scope.seg = $cookies.seg;
		$scope.inedit = {}; // object (associative array) indexed by designation
		$scope.flexRod = [];
		$scope.lotList = [];

		$scope.colors = ["red", "blue", "green"];
		$scope.sides = ["L", "R"];
		$scope.indexes = ["1", "2", "3", "4", "5", "6"]
		
		$scope.enableAll = {};
		$scope.lotAll = [];
		
		$scope.designations = [];
		for (var color = 0; color < $scope.colors.length; color++) 
			for (var side = 0; side < $scope.sides.length; side++) 
				for (var i = 1; i <= 6; i++)
					$scope.designations.push($scope.colors[color][0].toUpperCase() + "-" + 
							$scope.sides[side][0].toUpperCase() + i.toString());
		
		// initialize enableAll and lotAll associative array, indexed by color name
		for (var color = 0; color < $scope.colors.length; color++) {
			$scope.enableAll[$scope.colors[color]] = true;
			$scope.lotAll[$scope.colors[color]] = "";
		}

		for (var d in $scope.designations) {
			$scope.flexRod[$scope.designations[d]] = {"lot": "", "timestamp": "no value yet"};
			$scope.inedit[$scope.designations[d]] = false;
		}
				
				
		$scope.getLotList = function() {
			var url = '../lib/SRDb.php?dbcmnd=getLotsJSON&type=flex_rods&activeOnly=true';
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
		
		$scope.getLotList();
			
		$scope.update = function() {
			var url = '../lib/SRDb.php?dbcmnd=getInstalledLotNosJSON&type=flex_rod&seg=' + $scope.seg;
			//$log.log(url);
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					//$log.log(data);
					for (var i = 0; i < data.length; i++) {
						$scope.flexRod[data[i].pos].lot = data[i].lot;
						$scope.flexRod[data[i].pos].timestamp = data[i].timestamp;
						// disable applyAll for this color
						switch (data[i].pos[0]) {
							case 'R': color = "red"; break;
							case 'B': color = "blue"; break;
							case 'G': color = "green"; break;
						}
						$scope.enableAll[color] = false;
					}
				}
			);
		}

		$scope.edit = function(designation) {
			
			if (!$scope.seg || $scope.seg.length < 3 || $scope.seg.indexOf("unk") >= 0) {
				$window.alert("There is no segment currently selected.");
				return;
			}
			
			$scope.inedit[designation] = true;
		}
		
		$scope.cancel = function(designation) {
			$scope.inedit[designation] = false;
		}
		
		$scope.save = function(designation) {
			
			if ($scope.flexRod[designation].lot.length < 8) {
				$window.alert("No lot number selected.");
				$scope.update();
				return;
			}
			
			var url = "../lib/SRDb.php?dbcmnd=updateInstalledLotNo&type=flex_rod&seg=" + $scope.seg +
					"&pos=" + designation + "&lot=" + $scope.flexRod[designation].lot;
			url = url.replace(/[\s]/g, '%20');
			url = url.replace(/[#]/g, '%23');
			
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					$scope.inedit[designation] = false;
					$scope.update();				
				}
			);
		}
		
		$scope.saveAll = function(color) {
			if ($scope.lotAll[color].length < 8) {
				$window.alert("Please select lot number.");
				return;
			}
			for (var d in $scope.designations) {
				if ($scope.designations[d][0].toLowerCase() == color[0].toLowerCase()) {	
					var url = "../lib/SRDb.php?dbcmnd=updateInstalledLotNo&type=flex_rod&seg=" + $scope.seg +
						"&pos=" + $scope.designations[d] + "&lot=" + $scope.lotAll[color];
					url = url.replace(/[\s]/g, '%20');
					url = url.replace(/[#]/g, '%23');
					//$log.log(url);
					$http.get(url).success(
						function(data) {
							if (data.indexOf("error") >= 0) {
								$window.alert(data)
								return;
							}	
							$scope.inedit[$scope.designations[d]] = false;
							$scope.update();				
						}
					);
				}
			}
		}

		$timeout(function(){$scope.seg=$cookies.seg;}, 1000);
		$timeout($scope.update(), 1100);
	}
]);
