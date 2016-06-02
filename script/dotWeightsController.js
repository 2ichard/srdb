//'use strict';

var srdbDotWeightsApp = angular.module('srdbDotWeightsApp', ['ngCookies']);

srdbDotWeightsApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbDotWeightsApp.controller('DotWeightsCtrl', ['$scope', '$http', '$window', '$log', 
		'$interval', '$cookies', '$timeout',

	function($scope, $http, $window, $log, $interval, $cookies, $timeout) {
	
		$scope.name = "DotWeightCtrl";
		$scope.inedit = [];
		$scope.tech = "unknown";
		$scope.dotNames = ['Tare', 'Dot 1', 'Dot 2', 'Dot 3', 'Dot 4', 'Dot 5', 'Dot 6'];
		//$scope.dotNames = ['Tare', 'Dot 1', 'Dot 2', 'Dot 3', 'Dot 4', 'Dot 5', 'Dot 6', 'Ref Wt'];
		$scope.dotData = [];
		$scope.config = {"seg": "unk", "proc": "unk", "ver": "unk", "sec": "unk", "step": -1};
		
		// initialize dotData nd inedit arrays
		for (var i = 0; i < $scope.dotNames.length; i++) {
			$scope.dotData[$scope.dotNames[i]] = 
				{"dotName": $scope.dotNames[i], "measuredWeight": 0.0, 
				"dotWeight": 0.0, "tech": "who", "timestamp": ""};
			$scope.inedit[$scope.dotNames[i]] = false;
		}
		
		$scope.init = function(proc, ver, sec, step) {
			$scope.config.proc = proc;
			$scope.config.ver = ver;
			$scope.config.sec = sec;
			$scope.config.step = step;
			$scope.config.seg = $cookies.seg;
			if (!$scope.config.seg) $scope.config.seg = "unknown";
		}
		
		$scope.calcDotWeights = function() {
			
			if ($scope.dotData['Tare'].measuredWeight <= 0.0 || $scope.dotData['Dot 1'].measuredWeight <= 0.0)
				return;
				
			$scope.dotData['Dot 1'].dotWeight = 
					$scope.dotData['Dot 1'].measuredWeight - $scope.dotData['Tare'].measuredWeight;
					
			if ($scope.dotData['Dot 2'].measuredWeight <= 0.0)
				return;
			$scope.dotData['Dot 2'].dotWeight = 
					$scope.dotData['Dot 2'].measuredWeight - $scope.dotData['Dot 1'].measuredWeight;
					
			if ($scope.dotData['Dot 3'].measuredWeight <= 0.0)
				return;
			$scope.dotData['Dot 3'].dotWeight = 
					$scope.dotData['Dot 3'].measuredWeight - $scope.dotData['Dot 2'].measuredWeight;
					
			if ($scope.dotData['Dot 4'].measuredWeight <= 0.0)
				return;
			$scope.dotData['Dot 4'].dotWeight = 
					$scope.dotData['Dot 4'].measuredWeight - $scope.dotData['Dot 3'].measuredWeight;
					
			if ($scope.dotData['Dot 5'].measuredWeight <= 0.0)
				return;
			$scope.dotData['Dot 5'].dotWeight = 
					$scope.dotData['Dot 5'].measuredWeight - $scope.dotData['Dot 4'].measuredWeight;
					
			if ($scope.dotData['Dot 6'].measuredWeight <= 0.0)
				return;
			$scope.dotData['Dot 6'].dotWeight = 
					$scope.dotData['Dot 6'].measuredWeight - $scope.dotData['Dot 5'].measuredWeight;
					
//			if ($scope.dotData['Ref Wt'].measuredWeight <= 0.0)
//				return;
//			$scope.dotData['Ref Wt'].dotWeight = 
//					$scope.dotData['Ref Wt'].measuredWeight - $scope.dotData['Dot 6'].measuredWeight;
		}
		
		$scope.update = function() {
			var url = '../lib/SRDb.php?dbcmnd=getDotWeightsJSON&seg=' + $scope.config.seg +
					'&proc=' + $scope.config.proc + '&ver=' + $scope.config.ver +
					'&sec=' + $scope.config.sec + '&step=' + $scope.config.step;
			//$log.log(url);
			$http.get(url).success(
				function(data) {
					for (var i = 0; i < data.length; i++) {
						$scope.dotData[data[i].name].measuredWeight = data[i].weight;
						$scope.dotData[data[i].name].timestamp = data[i].timestamp;
					}
					$scope.calcDotWeights();
				}
			);
		}
		
		//$scope.calcDotWeights();
		
		$scope.edit = function(dotName) {
			
			if (!$scope.config.seg || $scope.config.seg.length < 3 || $scope.config.seg.indexOf("unk") >= 0) {
				$window.alert("There is no segment S/N currently selected.");
				return;
			}
			$scope.inedit[dotName] = true;
		}
		
		$scope.save = function(dotName) {

			var url = "../lib/SRDb.php?dbcmnd=updateDotWeight&seg=" + $scope.config.seg +
					"&proc=" + $scope.config.proc + "&ver=" + $scope.config.ver +
					"&sec=" + $scope.config.sec + "&step=" + $scope.config.step +
					"&name=" + dotName + "&weight=" + $scope.dotData[dotName].measuredWeight;
			url = url.replace(/[\s]/g, '%20');
			url = url.replace(/[#]/g, '%23');
			$http.get(url).success(
				function(data) {
					$scope.inedit[dotName] = false;
					$scope.update();
				}
			);
		}
		
		$scope.cancel = function(dotName) {
			$scope.inedit[dotName] = false;
		}
		
		$interval(function() {
			//$scope.segType = $cookies.seg_type;
			//$scope.seg = $cookies.seg;
			$scope.tech = $cookies.tech;
		}, 500);
		
		$timeout($scope.update, 1000);
	}
]);
