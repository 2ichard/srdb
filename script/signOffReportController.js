'use strict';

var srdbSignOffReportApp = angular.module('srdbSignOffReportApp', []);

srdbSignOffReportApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbSignOffReportApp.controller('SignOffReportCtrl', ['$scope', '$http', '$log', '$window', '$interval',

	function($scope, $http, $log, $window, $interval) {
	
		$scope.name = 'SignOffReportCtrl';
		$scope.seg = "All";
		$scope.sns = ['All'];
		$scope.segType = "All";
		$scope.segTypes = ['All', 1, 2, 3, 4, 5, 6];
		$scope.segs = [];
		$scope.proc = 'All';
		$scope.procs = ['All'];
		$scope.signoffs = [];
		$scope.orderProp = "proc";
		$scope.typeLabelMap = {};
		$scope.lots = ['All'];
		$scope.lot = 'All';
		
		$http.get('../lib/SRDb.php?dbcmnd=getsegsjson&type=all').success(
			function(data) {
				$scope.segs = data;
			}
		);
		
		$http.get('../lib/SRDb.php?dbcmnd=getLotsJSON&type=all').success(
			function(data) {
				if (data.indexOf("error") >= 0) {
					$window.alert(data)
					return;
				}
				for (var i = 0; i < data.length; i++)
					$scope.lots.push(data[i].lot);
			}
		);
			
		$scope.segChanged = function() {
			$scope.lot = 'All';
			$scope.update();	
		}
		
		$scope.typeChanged = function() {
			$scope.lot = 'All';
			$scope.sns = [];
			$scope.sns.push("All");
			if ($scope.segType != "All")
				for (var i = 0; i < $scope.segs.length; i++)
					if ($scope.segs[i].type == $scope.segType)
						$scope.sns.push($scope.segType + "-" + $scope.segs[i].sn);		
			$scope.update();
		}
		
		$scope.procChanged = function() {
			$scope.update();
		}
		
		$scope.lotChanged = function() {
			$scope.segType = 'All';
			$scope.seg = 'All';	
			$scope.update();
		}
			
		$scope.getSignOffDesc = function() {
			$http.get('../lib/SRDb.php?dbcmnd=getSignOffDescJSON&type=all').success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					for (var i = 0; i < data.length; i++)
						$scope.typeLabelMap[data[i].type] = data[i].title;
				}
			);
		}
		
		$scope.getSignOffDesc();
		
		// get list of known procedures
		$http.get('../lib/SRDb.php?dbcmnd=getProcsJSON').success(
			function(data) {
				//$log.log(data);
				for (var i = 0; i < data.length; i++) {
					//$log.log(data[i].num);
					$scope.procs.push(data[i].num);
				}
				//$log.log($scope.procs);
			}
		);
		
		$scope.update = function() {
			//$log.log('update type = ' + $scope.segType + ' seg = ' + $scope.seg);
			var url = '';
			if ($scope.seg != "All")
				url = '../lib/SRDb.php?dbcmnd=getSignOffsJSON&seg=' + $scope.seg  + '&proc=' + $scope.proc + '&ver=all';
			else if ($scope.lot != "All")
				url = '../lib/SRDb.php?dbcmnd=getSignOffsJSON&seg=' + $scope.lot  + '&proc=' + $scope.proc + '&ver=all';
			else
				url = '../lib/SRDb.php?dbcmnd=getSignOffsJSON&seg=all&proc=' + $scope.proc + '&ver=all';
			//$log.log(url);
			
			$http.get(url).success(
				function(data) {
					//$log.log(data);
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					
					$scope.signoffs = [];

					if ($scope.seg == "All" && $scope.segType != "All") {
						$log.log('just one type');
						var prefix = $scope.segType + '-';
						for (var i = 0; i < data.length; i++) {
							if (data[i].seg.indexOf(prefix) == 0)
								$scope.signoffs.push(data[i]);
						}
					}
					else
						for (var i = 0; i < data.length; i++)
							//if (data[i].seg.length < 6)
								$scope.signoffs.push(data[i]);
		
					
					// replace sign-off type with title
					for (var i = 0; i < $scope.signoffs.length; i++)
						$scope.signoffs[i].type = $scope.typeLabelMap[$scope.signoffs[i].type];
				}
			);
		}
		
		$scope.update();
//	
//		$interval(function() { 
//			$scope.update();
//		}, 1000);
	}
]);
