'use strict';

var srdbRadialPadsApp = angular.module('srdbEtchSolnUsageApp', ['ngCookies']);

srdbRadialPadsApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbRadialPadsApp.controller('EtchSolnUsageCtrl', ['$scope', '$http', '$window', '$log', '$timeout', '$cookies',

	function($scope, $http, $window, $log, $timeout, $cookies) {
	
		$scope.name = "EtchSolnUsageCtrl";
		$scope.seg = "unk";
		$scope.inedit = [];
		$scope.inedit['axial'] = [];
		$scope.inedit['radial'] = [];
		$scope.soln = [];
		$scope.soln['radial'] = [];
		$scope.soln['axial'] = [];
		$scope.lotList = [];
		$scope.enableAll = true;
		$scope.lotAll = "";
		$scope.orderProp = "pos.lot";
		
		$scope.positions = ["R1", "R2", "R3", "R4", "R5", "R6"];
		
		$scope.soln['radial'][1] = {"lot": "no value yet", "vol": 0, "timestamp": ""};
		$scope.soln['radial'][2] = {"lot": "no value yet", "vol": 0, "timestamp": ""};
		$scope.soln['axial' ][1] = {"lot": "no value yet", "vol": 0, "timestamp": ""};
		$scope.inedit['radial'][1] = false;
		$scope.inedit['radial'][2] = false;
		$scope.inedit['axial'][1] = false;
		
		
		$scope.update = function() {
			if ($scope.seg.length < 3 || $scope.seg == "unknown") return;
			var url = '../lib/SRDb.php?dbcmnd=getEtchSolnUsageJSON&seg=' + $scope.seg;
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					for (var i = 0; i < data.length; i++) {
						$scope.soln[data[i].loc][data[i].batch].timestamp = data[i].timestamp;
						$scope.soln[data[i].loc][data[i].batch].lot = data[i].lot;
						$scope.soln[data[i].loc][data[i].batch].vol = data[i].vol
					}
				}
			);
		}
				
		$scope.getLotList = function() {
			var url = '../lib/SRDb.php?dbcmnd=getLotsJSON&type=etch_soln&activeOnly=true';
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
				
		$scope.edit = function(loc, batch) {
			
			if (!$cookies.seg || $cookies.seg.length < 3 || $cookies.seg == "unknown") {
				$window.alert("There is no segment S/N currently selected.");
				return;
			}
			
			if ($scope.inedit['axial'][1] == true) {
				$window.alert("Finish editing axial hole usage first.");
				return;
			}
			else {
				var n;
				for (n = 1; n <= 2; n++) {
					if ($scope.inedit['radial'][n] == true) {
						$window.alert("Finish editing radial hole batch " + n + " usage first.");
						return;
					}
				}
			}
			$scope.inedit[loc][batch] = true;
		}
		
		$scope.cancel = function(loc, batch) {
			$scope.inedit[loc][batch] = false;
		}
		
		$scope.save = function(loc, batch) {
			
			if (!$cookies.seg || $cookies.seg.length < 3 || $cookies.seg == "unknown") {
				$window.alert("There is no segment S/N currently selected.");
				return;
			}
			
			if ($scope.soln[loc][batch].vol <= 0) {
				$window.alert("Enter volume used first.");
				return;
			}
			
			if (isNaN(parseInt($scope.soln[loc][batch].lot ))) {
				$window.alert("Enter lot number first.");
				return;
			}
			
			var url = "../lib/SRDb.php?dbcmnd=updateEtchSolnUsage&seg=" + $scope.seg + 
					"&loc=" + loc + "&batch=" + batch + 
					"&lot=" + $scope.soln[loc][batch].lot + "&vol=" + $scope.soln[loc][batch].vol;
			url = url.replace(/[\s]/g, '%20');
			url = url.replace(/[#]/g, '%23');
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					$scope.inedit[loc][batch] = false;
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
