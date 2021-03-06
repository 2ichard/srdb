'use strict';

var srdbMainPageApp = angular.module('srdbMainPageApp', ['ngCookies']);

srdbMainPageApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbMainPageApp.controller('MainCtrl', 

	['$scope', '$cookies', '$interval', '$http', '$window', '$log', '$timeout',

		function($scope, $cookies, $interval, $http, $window, $log, $timeout) {
		
			$scope.name = "MainCtrl";
			$scope.variant = "";
			$scope.content = "unknown";
			$scope.contentCookie = "unknown";
			$scope.showContentSetting = "true";
			
			$scope.proceduresVisible = false;
			$scope.procedures = [];
			$scope.signOffListVisible = false;
			$scope.signOffList = [];
			$scope.segList = [];
		
//			$scope.setContent = function() {
//				$log.log("setContent, content=" + $scope.content);
//				$cookies.content = $scope.content;
//			}
		
			$scope.setContent = function(content) {
				$log.log(content);
				$scope.content = content;
				$cookies.content = $scope.content;
			}
			
			$scope.hideContentSetting = function() {
				$log.log("hiding content setting");
				$timeout(function() { $scope.showContentSetting = false; }, 2000);
			}
			
			$scope.getSegList = function() {
				var url = './lib/SRDb.php?dbcmnd=getSegsJSON&type=all';
				//$log.log(url);
				$http.get(url).success(
					function(data) {
						//$log.log(data);
						for (var i = 0; i < data.length; i++) {
							$scope.segList[i] = data[i].type + "-" + data[i].sn;
						}
					}
				);
			}
			$scope.getSegList();
		
			$scope.showProcedures = function() {
				$scope.showNoTable();
				$scope.proceduresVisible = true;
				$http.get('./lib/SRDb.php?dbcmnd=getAllProcs').success(
					function(data) {
						$scope.procedures = data;
					}
				);
			}
			
			$scope.showSignOffs = function() {
				$window.alert("Not implemented yet.");
			}
			
			$scope.showSignOffList = function() {
				$log.log("show sign off list");
				$scope.showNoTable();
				$scope.signOffListVisible = true;
				$http.get('./lib/SRDb.php?dbcmnd=getSignOffListJSON').success(
					function(data) {
						$scope.signOffList = data;
					}
				);
			}
			
			$scope.showNoTable = function() {
				$scope.proceduresVisible = false;
				$scope.signOffListVisible = false;
			}
			
			$interval(function() {
				$scope.contentCookie = $cookies.content;
				if (!$scope.contentCookie)
					$scope.content = "unknown";
				else if ($scope.contentCookie.length < 3)
					$scope.content = "unknown";
				else
					$scope.content = $scope.contentCookie;
			}, 500);
		}
	]
);

srdbMainPageApp.controller('ProcTableCtrl', 

	['$scope', '$cookies', '$interval', '$http', '$window', '$log',

		function($scope, $cookies, $interval, $http, $window, $log) {
		
			$scope.name = "ProcTableCtrl";
			
		}
	]
);
