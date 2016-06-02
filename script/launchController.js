//'use strict';

var srdbLaunchApp = angular.module('srdbLaunchApp', ['ngCookies']);

srdbLaunchApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbLaunchApp.controller('LaunchCtrl', ['$scope', '$cookies', '$interval', '$http', '$window', '$log', '$timeout',

	function($scope, $cookies, $interval, $http, $window, $log, $timeout) {
		
		$scope.techNames = [];
		$scope.segs = [];
		$scope.segType = $cookies.seg_type;
		$scope.segTypes = [1, 2, 3, 4, 5, 6];
		$scope.seg = $cookies.seg;
		$scope.sns = [];
		$scope.procedures = [];
		$scope.orderProp = 'order';
		$scope.lastActivity = "unknown";
		
		
		$scope.getProcList = function() {
			$http.get('../lib/SRDb.php?dbcmnd=segprocsjson').success(
				function(data) {
					$scope.procedures = data;
				}
			);
		}
		
		$scope.getProcList();
		

		$scope.updateTimes = function() {
			$log.log("updateTimes seg " + $scope.seg);
			for(var i = 0; i < $scope.procedures.length; i++) {
				var url = '../lib/SRDb.php?dbcmnd=getSignOffsJSON&seg=' + 
						$scope.seg + '&proc=' + $scope.procedures[i].num + '&ver=1.0';
				$http.get(url).success(
					function(data) {
						if (data[0]) {
							var completed = ' ';
							for (var k = 0; k < data.length; k++)
								if (data[k].type == 'proc_close_out') {
									completed = data[k].timestamp;
									break;
								}
									
							for (var j = 0; j < $scope.procedures.length; j++)
								if ($scope.procedures[j].num == data[0].proc) {
									$scope.procedures[j].started = data[data.length-1].timestamp;
									$scope.procedures[j].completed = completed;
									break;
								}
						}
					}
				);
			}
			var url = '../lib/SRDb.php?dbcmnd=getSignOffsJSON&seg=' + $scope.seg + '&proc=all&ver=1.0';
			$http.get(url).success(
				function(data) {
					//$log.log(data);
					if (data.length > 0)
						$scope.lastActivity = data[0].timestamp;
					else 
						$scope.lastActivity = "no activity";
				}
			);
			
		}
		
		$http.get('../lib/SRDb.php?dbcmnd=techsjson').success(
			function(data) {
				$scope.techs = data;
				for (var i = 0; i < $scope.techs.length; i++)
					$scope.techNames[i] = $scope.techs[i].name;
			}
		);
			
		$http.get('../lib/SRDb.php?dbcmnd=getsegsjson&type=all').success(
			function(data) {
				$scope.segs = data;
				$scope.setSns();
			}
		);
								
		$scope.techChanged = function() {
			$cookies.tech = $scope.tech;
		}
		
		$scope.typeChanged = function() {
			$cookies.seg_type = $scope.segType;
			$cookies.seg = "";
			$scope.setSns();
		}
		
		$scope.setSns = function() {
			$scope.sns = [];
			for (var i = 0; i < $scope.segs.length; i++)
				if ($scope.segs[i].type == $scope.segType)
					$scope.sns.push($scope.segType + "-" + $scope.segs[i].sn);
		}
	
		$scope.snChanged = function() {
			for (var i = 0; i < $scope.procedures.length; i++) {
				$scope.procedures[i].started = '';
				$scope.procedures[i].completed = '';
			}
			$cookies.seg = $scope.seg;
//			$scope.updateTimes();
		}
		
		$scope.setSns();
		
		$interval(function() {
			//$log.log("cookie seg_type = " + $cookies.seg_type + 
			//	" $scope.segType = " + $scope.segType);
//			$scope.segType = $cookies.seg_type;
//			$scope.seg = $cookies.seg;
//			$scope.tech = $cookies.tech;
		}, 1000);
		
		$interval(function() {
			$scope.updateTimes();
		}, 1000);
	}
]);

