//'use strict';

var srdbRadialHoleMeasApp = angular.module('srdbRadialHoleMeasApp', ['ngCookies']);

srdbRadialHoleMeasApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbRadialHoleMeasApp.controller('RadialHoleMeasCtrl', ['$scope', '$http', '$window', '$log', 
		'$interval', '$cookies', '$timeout', '$filter',

	function($scope, $http, $window, $log, $interval, $cookies, $timeout, $filter) {
	
		$scope.name = "RadialHoleMeasCtrl";
		$scope.inedit = [];
		$scope.tech = "unknown";
				
		$scope.locationNames = [ [], 
			['', '#1: T1 + 15\xB0 CW', '#2: 60\xB0 CW of #1', '#3: 60\xB0 CW of #2'],
			['', '#1: T1 + 15\xB0 CW', '#2: 60\xB0 CW of #1', '#3: 60\xB0 CW of #2'],
			['', '#1: T1', '#2: 60\xB0 CW of #1', '#3: 60\xB0 CW of #2'] ];

		$scope.locationData = [];
		$scope.config = {"seg": "unk", "proc": "unk", "ver": "unk", "sec": "unk", "step": -1};
		$scope.range = {};
		$scope.constants = {};
				

		$scope.init = function(seg, proc, ver, sec, step) {
			$scope.config.seg = seg;
			$scope.config.proc = proc;
			$scope.config.ver = ver;
			$scope.config.sec = sec;
			$scope.config.step = step;
		}
	
		$scope.range['A'] = { 'min': -0.0050,  'max': 0.0050 };
		$scope.range['B'] = { 'min':  0.00220, 'max': 0.01380 };
		$scope.range['D'] = { 'min':  0.00732, 'max': 0.02900 };
		$scope.range['F'] = { 'min':  9.0600,  'max': 9.0680 };
		
		$scope.constants = { 'EPk1': 0.0018, 'Gk1': 10.0000, 'Gk2': 2, 'Gk3': 0.446, 'Hk1': 0.028 };

		// initialize inedit array
		for (var part = 1; part <= 3; part++) {
			$scope.inedit[part] = [];
			for (var location = 1; location <= 3; location++) 
				$scope.inedit[part][location] = false;
		}
		
		// initialize locationData array
		for (var location = 1; location <= 3; location++)
			$scope.locationData[location] = {'A': '', 'B': '', 'C': '', 'D': '', 'E': '', 
					'EP': '', 'F': '', 'G': '', 'H': ''};
		
		$scope.update = function() {
			var url = '../lib/SRDb.php?dbcmnd=getRadialHoleMeasJSON&seg=' + $cookies.seg;
			//$log.log(url);
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					for (var i = 0; i < data.length; i++) {

						if (data[i].A > -999.9)
							$scope.locationData[data[i].location].A = data[i].A;

						if (data[i].B > -999.9)
							$scope.locationData[data[i].location].B = data[i].B;
	
						if ($scope.locationData[data[i].location].A != '' && $scope.locationData[data[i].location].B != '')
							$scope.locationData[data[i].location].C = 
									$scope.locationData[data[i].location].B - $scope.locationData[data[i].location].A;
									
						if (data[i].D > -999.9)
							$scope.locationData[data[i].location].D = data[i].D;
						
						if ($scope.locationData[data[i].location].D != '' && $scope.locationData[data[i].location].B != '')
							$scope.locationData[data[i].location].E = 
									$scope.locationData[data[i].location].D - $scope.locationData[data[i].location].B;
									
						if ($scope.locationData[data[i].location].E != '')
						$scope.locationData[data[i].location].EP = 
							$scope.locationData[data[i].location].E - $scope.constants['EPk1'];
							
						if (data[i].F > -999.9)
							$scope.locationData[data[i].location].F = data[i].F;
						
						if ($scope.locationData[data[i].location].D != '' && 
								$scope.locationData[data[i].location].F != '') {
							var D = $scope.locationData[data[i].location].D;
							var F = $scope.locationData[data[i].location].F;
							$scope.locationData[data[i].location].G = 
							((D + $scope.constants['Gk1'] - F) / $scope.constants['Gk2']) - $scope.constants['Gk3'];
						}
						
						if ($scope.locationData[data[i].location].G != '')
							$scope.locationData[data[i].location].H = 
								$scope.locationData[data[i].location].G - $scope.constants['Hk1'];
					}
				}
			);
		}
			
		$scope.edit = function(part, location) {
			if (!$cookies.seg || $cookies.seg.length < 3) {
				$window.alert("There is no segment S/N currently selected.");
				return;
			}
			$scope.inedit[part][location] = true;
		}
		
		$scope.savePart1 = function(location) {
			
			var okCancelMsg = "Click OK if you want to save value anyway,\notherwise click Cancel.";
			var aMsg = "Measurement A is out of range (" + $scope.range['A'].min.toString();
			aMsg += " - " + $scope.range['A'].max + "\").\n";
			aMsg += okCancelMsg;
			var bMsg = "Measurement B is out of range (" + $scope.range['B'].min.toString();
			bMsg += " - " + $scope.range['B'].max + "\").\n";
			bMsg += okCancelMsg;
					
			if ($scope.locationData[location].A == '') {
				$window.alert("Please enter a value for A.");
				return;
			}
			
			// save A
			if ($scope.locationData[location].A != '') {
				var a = Number($scope.locationData[location].A);
				if (a < $scope.range['A'].min || a > $scope.range['A'].max)
					if (!$window.confirm(aMsg)) {
						$scope.locationData[location].A = '';
						return;
					}
				$scope.save(location, 'A', $scope.locationData[location].A);
			}
			
			// save B
			if ($scope.locationData[location].B != '') {
				var b = Number($scope.locationData[location].B);
				if (b < $scope.range['B'].min || b > $scope.range['B'].max)
					if (!$window.confirm(aMsg)) {
						$scope.locationData[location].B = '';
						return;
					}
				$scope.save(location, 'B', $scope.locationData[location].B);
			}
			
			$scope.cancel(1, location);	
			$scope.update();
		}
		
		$scope.savePart2 = function(location) {
			
			var okCancelMsg = "Click OK if you want to save value anyway,\notherwise click Cancel.";
			var dMsg = "Measurement D is out of range (" + $scope.range['D'].min.toString();
			dMsg += " - " + $scope.range['D'].max + "\").\n";
			dMsg += okCancelMsg;
					
			if ($scope.locationData[location].D == '') {
				$window.alert("Please enter a value for D.");
				return;
			}
			
			// save D
			var d = Number($scope.locationData[location].D);
			if (d < $scope.range['D'].min || d > $scope.range['D'].max)
				if (!$window.confirm(dMsg)) {
					$scope.locationData[location].D = '';
					return;
				}
			$scope.save(location, 'D', $scope.locationData[location].D);
			
			$scope.cancel(2, location);	
			$scope.update();
		}
		
		$scope.savePart3 = function(location) {
			
			var okCancelMsg = "Click OK if you want to save value anyway,\notherwise click Cancel.";
			var fMsg = "Measurement F is out of range (" + $scope.range['F'].min.toString();
			fMsg += " - " + $scope.range['F'].max + "\").\n";
			fMsg += okCancelMsg;
					
			if ($scope.locationData[location].F == '') {
				$window.alert("Please enter a value for F.");
				return;
			}
			
			// save F
			var f = Number($scope.locationData[location].F);
			if (f < $scope.range['F'].min || f > $scope.range['F'].max)
				if (!$window.confirm(fMsg)) {
					$scope.locationData[location].F = '';
					return;
				}
			$scope.save(location, 'F', $scope.locationData[location].F);
			
			$scope.cancel(3, location);	
			$scope.update();
		}
		
		$scope.save = function(location, meas_type, meas_val) {	
					
			var url = "../lib/SRDb.php?dbcmnd=updateRadialHoleMeas&seg=" + $scope.config.seg +
					"&proc=" + $scope.config.proc + "&ver=" + $scope.config.ver + "&sec=" + 
					$scope.config.sec + "&step=" + $scope.config.step +
					"&location=" + location + "&meas_type=" + meas_type + "&meas_val=" + meas_val;
			url = url.replace(/[\s]/g, '%20');
			url = url.replace(/[#]/g, '%23');
			//$log.log(url);
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					$scope.update();
				}
			);
		}
		
		$scope.cancel = function(part, location) {
			$scope.inedit[part][location] = false;
			//$scope.update();
		}

		$timeout($scope.update, 1000);
	}
]);
