'use strict';

var srdbBatchLaunchApp = angular.module('srdbBatchLaunchApp', ['ngCookies']);

srdbBatchLaunchApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);


srdbBatchLaunchApp.controller('BatchLaunchCtrl', 

	['$scope', '$cookies', '$interval', '$http', '$window', '$log', '$filter', '$timeout',

		function($scope, $cookies, $interval, $http, $window, $log, $filter, $timeout) {
		
			$scope.procs = [];
			$scope.existingLots = [];
			$scope.selectedLot = [];
			
			$scope.techNames = [];
			$scope.segs = [];
			$scope.segType = $cookies.seg_type;

			$scope.seg = $cookies.seg;
			$scope.sns = [];
				
			$http.get('../lib/SRDb.php?dbcmnd=techsjson').success(
				function(data) {
					$scope.techs = data;
					for (var i = 0; i < $scope.techs.length; i++)
						$scope.techNames[i] = $scope.techs[i].name;
				}
			);
			
			$scope.techChanged = function() {
				$cookies.tech = $scope.tech;
			}	
		
			$scope.getExistingLots = function(procNum) {
				//$log.log("get existing for " + procNum);
				var url = '../lib/SRDb.php?dbcmnd=getLotsJSON&type=' + 
							$scope.procNumToType(procNum);
				//$log.log(url);
				$http.get(url).success(
					function(data) {
						var lotList = [];
						for (var i = 0; i < data.length; i++)
							lotList[i] = data[i].lot;
						$scope.existingLots[procNum] = lotList;
					}
				)
			}
			
			$scope.getLotLists = function() {
				for (var i = 0; i < $scope.procs.length; i++) 
					$scope.getExistingLots($scope.procs[i].num);
			}
				
			$scope.getProcs = function() {
				$http.get('../lib/SRDb.php?dbcmnd=getBatchProcsJSON').success(
					function(data) {
						$scope.procs = data;
						$scope.getLotLists();
					}
				);
			}
						
			$scope.getProcs();
						
			$scope.procNumToType = function(procNum) {
				switch(procNum) {
					case "114":
					case "SRP114":
						return("axial_inserts");
					case "124":
					case "SRP124":
						return("etch_soln");
					case "125":
					case "SRP125":
					case "889":
					case "SRP889":
						return("flex_rods");
					case "SRP999":
						return("foo_widgets");
					default:
						return("unknown");
				}
			}
			
			$scope.createNew = function(procNum) {	
				$http.get('../lib/SRDb.php?dbcmnd=getNextLotJSON').success(
					function(data) {
						//$log.log("getLotNum " + data[0].lot);
						$cookies.lot_num = data[0].lot;
						var url = '../lib/SRDb.php?dbcmnd=addLot&type=' + 
							$scope.procNumToType(procNum) + '&num=' + data[0].lot;
						//$window.alert(url);
						$http.get(url).success(
							function(data) {
								if (data.indexOf("error") >= 0) {
									$window.alert(data)
									return;
								}
								$window.alert('Lot ' + $cookies.lot_num + 'of type ' + $scope.procNumToType(procNum) + ' created.');
								$scope.getLotLists();
							}
						).error(
							function(data) {
								$window.alert('Lot ' + $cookies.lot_num + 'of type ' + $scope.procNumToType(procNum) + ' created.');
							}
						);
						$window.location.href = "BatchProcedurePage.php?num=" + 
								procNum + "&content=" + $cookies.content;
					}
				);
			}
			
			$scope.openProcedure = function(procNum) {
				if (!$scope.selectedLot[procNum] || $scope.selectedLot[procNum].length < 1) {
					$window.alert("Please select a lot number\nfor procedure " + procNum + " first.");
					return;
				}
				$cookies.lot_num = $scope.selectedLot[procNum];
				$window.location.href = "BatchProcedurePage.php?num=" + 
						procNum + "&content=" + $cookies.content;
			}
			
			$interval(function() {
				$scope.activeLotNum = $cookies.lot_num;
				$scope.tech = $cookies.tech;
				if (!$scope.tech)
					$scope.tech = "unknown";
			}, 1000);
		}
	]
);