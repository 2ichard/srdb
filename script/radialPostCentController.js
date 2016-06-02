//'use strict';

var srdbRadialPostCentApp = angular.module('srdbRadialPostCentApp', ['ngCookies']);

srdbRadialPostCentApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbRadialPostCentApp.controller('RadialPostCentCtrl', ['$scope', '$http', '$window', '$log', 
		'$interval', '$cookies', '$timeout',

	function($scope, $http, $window, $log, $interval, $cookies, $timeout) {
	
		$scope.name = "RadialPostCentCtrl";
		$scope.inedit = [];
		$scope.tech = "unknown";
		$scope.locData = [];
		$scope.config = {"seg": "unk", "proc": "unk", "ver": "unk", "sec": "unk", "step": -1};
		
		// initialize dotData and inedit arrays
		for (var pos = 1; pos <= 3; pos++) {
			$scope.locData[pos] = 
				{ "label": "", "gap": 0.0, "diff": 0.0 };
			$scope.inedit[pos] = false;
		}
		
		$scope.locData[1].label = "Loc. #1: 1st position CW\nfrom radial post guide pin";
		$scope.locData[2].label = "Loc. #2: 120\xB0 CW from #1";
		$scope.locData[3].label = "Loc. #3: 120\xB0 CW from #2";

		$scope.init = function(proc, ver, sec, step) {
			$scope.config.proc = proc;
			$scope.config.ver = ver;
			$scope.config.sec = sec;
			$scope.config.step = step;
			$scope.config.seg = $cookies.seg;
			if (!$scope.config.seg) $scope.config.seg = "unknown";
		}
		
		$scope.calcDiffs = function() {
			$scope.locData[1].diff = $scope.locData[2].gap - $scope.locData[1].gap;
			if ($scope.locData[1].diff < 0.0) $scope.locData[1].diff *= -1.0;

			$scope.locData[2].diff = $scope.locData[3].gap - $scope.locData[2].gap;
			if ($scope.locData[2].diff < 0.0) $scope.locData[2].diff *= -1.0;

			$scope.locData[3].diff = $scope.locData[1].gap - $scope.locData[3].gap;
			if ($scope.locData[3].diff < 0.0) $scope.locData[3].diff *= -1.0;
		}
		
		$scope.update = function() {
			var url = '../lib/SRDb.php?dbcmnd=getRadialPostCentJSON&seg=' + $cookies.seg +
					'&proc=' + $scope.config.proc + '&ver=' + $scope.config.ver +
					'&sec=' + $scope.config.sec + '&step=' + $scope.config.step;
			$log.log(url);
			$http.get(url).success(
				function(data) {
					$log.log(data);
					for (var i = 0; i < data.length; i++) {
						$scope.locData[data[i].loc].gap = data[i].gap;
					}
					$scope.calcDiffs();
				}
			);
		}
				
		$scope.edit = function(loc) {
			
			if (!$scope.config.seg || $scope.config.seg.length < 3 || $scope.config.seg.indexOf("unk") >= 0) {
				$window.alert("There is no segment S/N currently selected.");
				return;
			}
			
			for (var i = 1; i <= 3; i++)
				if ($scope.inedit[i] == true) {
					$window.alert("Finish edit already in progress first.");
					return;
				}
			$scope.inedit[loc] = true;
		}
		
		$scope.save = function(loc) {
			if (!$cookies.seg || $cookies.seg.length < 3) {
				$window.alert("There is no segment S/N currently selected.");
				return;
			}
			var url = "../lib/SRDb.php?dbcmnd=updateRadialPostCent&seg=" + $cookies.seg +
					"&proc=" + $scope.config.proc + "&ver=" + $scope.config.ver +
					"&sec=" + $scope.config.sec + "&step=" + $scope.config.step +
					"&loc=" + loc + "&gap=" + $scope.locData[loc].gap;
			url = url.replace(/[\s]/g, '%20');
			url = url.replace(/[#]/g, '%23');
			$log.log(url);
			$http.get(url).success(
				function(data) {
					$scope.inedit[loc] = false;
					$scope.update();
				}
			);
		}
		
		$scope.cancel = function(loc) {
			$scope.inedit[loc] = false;
		}
		
		$interval(function() {
			//$scope.segType = $cookies.seg_type;
			//$scope.seg = $cookies.seg;
			$scope.tech = $cookies.tech;
		}, 500);
		
		$timeout($scope.update, 1000);
	}
]);
