//'use strict';

var srdbAlignmentFixtureIdsApp = angular.module('srdbAlignmentFixtureIdsApp', ['ngCookies']);

srdbAlignmentFixtureIdsApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbAlignmentFixtureIdsApp.controller('AlignmentFixtureIdsCtrl', ['$scope', '$http', '$window', '$log', 
		'$interval', '$cookies', '$timeout',

	function($scope, $http, $window, $log, $interval, $cookies, $timeout) {
	
		$scope.name = "DotWeightCtrl";
		$scope.inedit = [];
		$scope.tech = "unknown";
		$scope.pointNames = ['P1', 'P2', 'P3', 'P4', 'P5', 'P6'];
		//$scope.innerFixtureIds = ['IAF-1', 'IAF-2', 'IAF-3', 'IAF-4', 'IAF-5', 'IAF-6'];
		//$scope.outerFixtureIds = ['OAF-1', 'OAF-2', 'OAF-3'];
		//$scope.t1FixtureIds = ['T1-1', 'T1-2', 'T1-3'];
		$scope.fixtureIds = [];
		$scope.pointData = [];
		$scope.fixtureType = [];
		$scope.config = {"seg": "unk", "proc": "unk", "ver": "unk", "sec": "unk", "step": -1};
				

		$scope.init = function(seg, proc, ver, sec, step) {
			$scope.config.seg = $cookies.seg;
			if (!$scope.config.seg) $scope.config.seg = "unknown";
			$scope.config.proc = proc;
			$scope.config.ver = ver;
			$scope.config.sec = sec;
			$scope.config.step = step;
		}
		
		for (var i = 0; i < $scope.pointNames.length; i++) {
			$scope.inedit[$scope.pointNames[i]] = false;
			$scope.pointData[$scope.pointNames[i]] = {'fixtureId': 'no value yet', 'timestamp': '-'};
			$scope.fixtureIds[$scope.pointNames[i]] = [
					$scope.pointNames[i] + '-1', 
					$scope.pointNames[i] + '-2', 
					$scope.pointNames[i] + '-3'];
		}
						
		$scope.fixtureType['P1'] = 'inner';
		$scope.fixtureType['P2'] = 'inner';
		$scope.fixtureType['P3'] = 'inner';
		$scope.fixtureType['P4'] = 'outer';
		$scope.fixtureType['P5'] = 't1';
		$scope.fixtureType['P6'] = 'outer';

		$scope.update = function() {
			var url = '../lib/SRDb.php?dbcmnd=getAlignmentFixtureIdsJSON&seg=' + $cookies.seg;
			$http.get(url).success(
				function(data) {
					for (var i = 0; i < data.length; i++) {
						$scope.pointData[data[i].point].fixtureId = data[i].fixture_id;
						$scope.pointData[data[i].point].timestamp = data[i].timestamp;
					}
				}
			);
		}
			
		$scope.edit = function(pointName) {
			
			if (!$scope.config.seg || $scope.config.seg.length < 3 || $scope.config.seg.indexOf("unkn") >= 0) {
				$window.alert("There is no segment S/N currently selected.");
				return;
			}
			
			$scope.inedit[pointName] = true;
		}
		
		$scope.save = function(pointName) {
			if (!$cookies.seg || $cookies.seg.length < 3) {
				$window.alert("There is no segment S/N currently selected.");
				return;
			}
			var url = "../lib/SRDb.php?dbcmnd=updateAlignmentFixtureId&seg=" + $cookies.seg +
					"&proc=" + $scope.config.proc + "&ver=" + $scope.config.ver +
					"&sec=" + $scope.config.sec + "&step=" + $scope.config.step +
					"&point=" + pointName + "&fixture_id=" + $scope.pointData[pointName].fixtureId;
			url = url.replace(/[\s]/g, '%20');
			url = url.replace(/[#]/g, '%23');
			$http.get(url).success(
				function(data) {
					$scope.inedit[pointName] = false;
					$scope.update();
				}
			);
		}
		
		$scope.cancel = function(pointName) {
			$scope.inedit[pointName] = false;
		}
		
		$interval(function() {
			//$scope.segType = $cookies.seg_type;
			$scope.seg = $cookies.seg;
			$scope.tech = $cookies.tech;
		}, 500);
		
		$timeout($scope.update, 1000);
	}
]);
