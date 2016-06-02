'use strict';

var srdbProcNotesReportApp = angular.module('srdbProcNotesReportApp', []);

srdbProcNotesReportApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbProcNotesReportApp.controller('ProcNotesReportCtrl', ['$scope', '$http', '$log', '$window', '$interval',

	function($scope, $http, $log, $window, $interval) {
	
		$scope.name = 'ProcNotesReportCtrl';
		$scope.seg = "All";
		$scope.sns = ['All'];
		$scope.segType = "All";
		$scope.segTypes = ['All', 1, 2, 3, 4, 5, 6];
		$scope.segs = [];
		$scope.proc = 'All';
		$scope.procs = ['All'];
		$scope.notes = [];
		$scope.orderProp = "seg";
		$scope.typeLabelMap = {};
		
		// get list of segment serial numbers
		$http.get('../lib/SRDb.php?dbcmnd=getsegsjson&type=all').success(
			function(data) {
				$scope.segs = data;
			}
		);
			
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
				
		$scope.typeChanged = function() {
			$scope.sns = [];
			$scope.sns.push("All");
			if ($scope.segType == 'All')
				$scope.seg = 'All';
			else 
				for (var i = 0; i < $scope.segs.length; i++)
					if ($scope.segs[i].type == $scope.segType)
						$scope.sns.push($scope.segType + "-" + $scope.segs[i].sn);		
			$scope.update();
		}
		
		$scope.procChanged = function() {
			$scope.update();
		}
	
		
		$scope.update = function() {
			var url = '../lib/SRDb.php?dbcmnd=getProcNotesJSON&&proc=' + $scope.proc + '&ver=all&seg=' + $scope.seg;
			//$log.log(url);
			
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					//$log.log(data);
					$scope.notes = data;

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

srdbProcNotesReportApp.filter('unsafe', function($sce) {
	return function(val) {
		return $sce.trustAsHtml(val);
	};
});
