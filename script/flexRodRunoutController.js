'use strict';

var srdbFlexRodRunoutApp = angular.module('srdbFlexRodRunoutApp', ['ngCookies']);

srdbFlexRodRunoutApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbFlexRodRunoutApp.controller('FlexRodRunoutCtrl', ['$scope', '$http', '$window', '$log', '$timeout', '$cookies',

	function($scope, $http, $window, $log, $timeout, $cookies) {
	
		$scope.name = "FlexRodRunoutCtrl";
		$scope.lotNum = "unk";
		$scope.inedit = {}; // object (associative array) indexed by designation
		$scope.flexRod = [];
		
		$scope.colors = ["red", "blue", "green"];
		$scope.sides = ["L", "R"];
		$scope.indexes = ["1", "2", "3", "4", "5", "6"]
		
		$scope.runoutRange = {"min": 0.0, "max": 0.030, "entryMax": 0.060};

		$scope.designations = [];
		for (var color = 0; color < $scope.colors.length; color++) 
			for (var side = 0; side < $scope.sides.length; side++) 
				for (var i = 1; i <= 6; i++)
					$scope.designations.push($scope.colors[color][0].toUpperCase() + "-" + 
							$scope.sides[side][0].toUpperCase() + i.toString());
		
		for (var d in $scope.designations) {
			$scope.flexRod[$scope.designations[d]] = {"runout": "", "timestamp": "no value yet"};
			$scope.inedit[$scope.designations[d]] = false;
		}
					
		$scope.update = function() {
			var url = '../lib/SRDb.php?dbcmnd=getFlexRodRunoutJSON&lot=' + $cookies.lot_num;
			//$log.log(url);
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					//$log.log(data);
					for (var i = 0; i < data.length; i++) {
						$scope.flexRod[data[i].designation].runout = data[i].runout;
						$scope.flexRod[data[i].designation].timestamp = data[i].timestamp;
					}
				}
			);
		}

		$scope.edit = function(designation) {
			
			if (!$scope.lotNum || $scope.lotNum.length < 3 || $scope.lotNum.indexOf("unk") >= 0) {
				$window.alert("There is no lot number currently selected.");
				return;
			}
			
			$scope.inedit[designation] = true;
		}
		
		$scope.cancel = function(designation) {
			$scope.inedit[designation] = false;
		}
		
		$scope.save = function(designation) {
			var msg = "Value is out of range (" + $scope.runoutRange.min + " - " 
					+ $scope.runoutRange.entryMax + "\").\n";
			msg += "Click OK if you want to save value anyway,\notherwise click Cancel.";
			if (Number($scope.flexRod[designation].runout) < $scope.runoutRange.min || 
				Number($scope.flexRod[designation].runout) > $scope.runoutRange.max) {
				if (!$window.confirm(msg)) {
					$scope.flexRod[designation].runout = "";
					$scope.update();
					return;
				}
			}
			
			var url = "../lib/SRDb.php?dbcmnd=updateFlexRodRunout&lot=" + $scope.lotNum +
					"&designation=" + designation + "&runout=" + $scope.flexRod[designation].runout;
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

		$timeout(function(){$scope.lotNum=$cookies.lot_num;}, 1000);
		$timeout(function(){$scope.update();}, 1100);
	}
]);
