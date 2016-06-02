'use strict';

var srdbAxialHoleMeasApp = angular.module('srdbAxialHoleMeasApp', ['ngCookies']);

srdbAxialHoleMeasApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbAxialHoleMeasApp.controller('AxialHoleMeasCtrl', ['$scope', '$http', '$window', '$log', '$timeout', '$cookies',

	function($scope, $http, $window, $log, $timeout, $cookies) {
	
		$scope.name = "FlexRodRunoutCtrl";
		
		$scope.seg = $cookies.seg;

		$scope.measType = "unk";
		$scope.measTypes = ['depth', 'diameter'];
		
		$scope.depthInedit = {}; // object (associative array) hole number
		$scope.diaInedit = {}; // object (associative array) hole number

		$scope.depth = [];
		$scope.dia = [];
		
		$scope.proc = "unk";
		$scope.ver = "unk";
		$scope.sec = -1;
		$scope.step = -1;
		
		$scope.depthRange = {"preMin": 1.502, "preMax": 1.562, "postMin": 1.572, "postMax": 1.652};
		$scope.diaRange = {"preMin": 0.70400, "preMax": 0.71400, "postMin": 0.70912, "postMax": 0.73000};

		$scope.holeNumbers = [];
		for (var i = 1; i <= 12; i++)
			$scope.holeNumbers.push("S" + i.toString());
		for (var i = 1; i <= 6; i++)
			for (var j = 1; j <= 6; j++)
				$scope.holeNumbers.push("W" + i.toString() + "-" + j.toString());

		// initialize depth, dia, depthInedit and diaInedit associative arrays
		for (var h in $scope.holeNumbers) {
			$scope.depth[$scope.holeNumbers[h]] = {
				"pre_grind": "", "post_grind": "", "removed": "", "timestamp": "no value yet"};
			$scope.dia[$scope.holeNumbers[h]] = {
				"pre_etch": "", "post_etch": "", "etched": "", "timestamp": "no value yet"};
			$scope.depthInedit[$scope.holeNumbers[h]] = false;
			$scope.diaInedit[$scope.holeNumbers[h]] = false;
		}
		
		$scope.init = function(proc, ver, sec, step) {
			$scope.proc = proc;
			$scope.ver = ver;
			$scope.sec = sec;
			$scope.step = step;
		}
					
		$scope.update = function() {
			var url = '../lib/SRDb.php?dbcmnd=getAxialHoleMeasJSON&seg=' + $cookies.seg;
			//$log.log(url);
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					for (var i = 0; i < data.length; i++) {
						//$log.log(data[i]);
						if (data[i].pre_grind_depth > 0.0)
							$scope.depth[data[i].hole].pre_grind = data[i].pre_grind_depth;
						if (data[i].post_grind_depth > 0.0)
							$scope.depth[data[i].hole].post_grind = data[i].post_grind_depth;
						if ($scope.depth[data[i].hole].pre_grind > 0 && $scope.depth[data[i].hole].post_grind > 0)
							$scope.depth[data[i].hole].removed = 
									$scope.depth[data[i].hole].post_grind - $scope.depth[data[i].hole].pre_grind;
									
						if (data[i].pre_etch_dia > 0.0)
							$scope.dia[data[i].hole].pre_etch = data[i].pre_etch_dia;
						if (data[i].post_etch_dia > 0.0)
							$scope.dia[data[i].hole].post_etch = data[i].post_etch_dia;
						if ($scope.dia[data[i].hole].pre_etch > 0 && $scope.dia[data[i].hole].post_etch > 0)
							$scope.dia[data[i].hole].etched = 
									$scope.dia[data[i].hole].post_etch - $scope.dia[data[i].hole].pre_etch;
						//$log.log($scope.depth[data[i].hole]);
					}
				}
			);
		}



		$scope.edit = function(measType, hole) {
			
			if (!$cookies.seg || $cookies.seg.length < 3 || $cookies.seg.indexOf("unk") >= 0) {
				$window.alert("There is no segment currently selected.");
				return;
			}
			
			switch(measType) {
				case 'depth':
					$scope.depthInedit[hole] = true;
					break;
				case 'diameter':
					$scope.diaInedit[hole] = true;
					break;
				default:
					$window.alert('Program Error: unrecognized measurement type.')
			}
		}
		
		$scope.cancel = function(measType, hole) {
			
			switch(measType) {
				case 'depth':
					$scope.depthInedit[hole] = false;
					break;
				case 'dia':
				case 'diameter':
					$scope.diaInedit[hole] = false;
					break;
				default:
					$window.alert('Program Error: unrecognized measurement type.')
			}
			
			$scope.diaInedit[hole] = false;
		}
		
		$scope.saveDepth = function(hole) {
			
			var okCancelMsg = "Click OK if you want to save value anyway,\notherwise click Cancel.";
			var preMsg = "Pre-grind depth is out of range (" + $scope.depthRange.preMin.toString();
			preMsg += " - " + $scope.depthRange.preMax + "\").\n";
			preMsg += okCancelMsg;
			var postMsg = "Post-grind depth is out of range (" + $scope.depthRange.postMin.toString();
			postMsg += " - " + $scope.depthRange.postMax + "\").\n";
			postMsg += okCancelMsg;
			
		
			if ($scope.depth[hole].pre_grind == '' && 	$scope.depth[hole].post_grind == '') {
				$window.alert("Please enter a value.");
				return;
			}
					
			// save pre-grind depth
			if ($scope.depth[hole].pre_grind != '') {
				var depth = Number($scope.depth[hole].pre_grind);
				if (depth < $scope.depthRange.preMin || depth > $scope.depthRange.preMax)
					if (!$window.confirm(preMsg)) {
						$scope.depth[hole].pre_grind = '';
						return;
					}
				$scope.save(hole, 'pre_grind_depth', $scope.depth[hole].pre_grind);
				$scope.cancel('depth', hole);
			}
					
			// save post-grind depth
			if ($scope.depth[hole].post_grind != '') {
				var depth = Number($scope.depth[hole].post_grind);
				if (depth < $scope.depthRange.postMin || depth > $scope.depthRange.postMax)
					if (!$window.confirm(postMsg)) {
						$scope.depth[hole].post_grind = '';
						return;
					}
				$scope.save(hole, 'post_grind_depth', $scope.depth[hole].post_grind);
				$scope.cancel('diameter', hole);
			}
		}
		
		
		$scope.saveDia = function(hole) {
			
			var okCancelMsg = "Click OK if you want to save value anyway,\notherwise click Cancel.";
			var preMsg = "Pre-etch diameter is out of range (" + $scope.diaRange.preMin.toString();
			preMsg += " - " + $scope.diaRange.preMax + "\").\n";
			preMsg += okCancelMsg;
			var postMsg = "Post-etch depth is out of range (" + $scope.diaRange.postMin.toString();
			postMsg += " - " + $scope.diaRange.postMax + "\").\n";
			postMsg += okCancelMsg;
			
		
			if ($scope.dia[hole].pre_etch == '' && 	$scope.dia[hole].post_etch == '') {
				$window.alert("Please enter a value.");
				return;
			}
					
			// save pre-etch diameter
			if ($scope.dia[hole].pre_etch != '') {
				var dia = Number($scope.dia[hole].pre_etch);
				if (dia < $scope.diaRange.preMin || dia > $scope.diaRange.preMax)
					if (!$window.confirm(preMsg)) {
						$scope.dia[hole].pre_etch = '';
						return;
					}
				$scope.save(hole, 'pre_etch_dia', $scope.dia[hole].pre_etch);
				$scope.cancel('dia', hole);
			}
					
			// save post-etch diameter
			if ($scope.dia[hole].post_etch != '') {
				var dia = Number($scope.dia[hole].post_etch);
				if (dia < $scope.diaRange.postMin || dia > $scope.diaRange.postMax)
					if (!$window.confirm(postMsg)) {
						$scope.depth[hole].post_grind = '';
						return;
					}
				$scope.save(hole, 'post_etch_dia', $scope.dia[hole].post_etch);
				$scope.cancel('dia', hole);
			}
		}
		
		
		$scope.save = function(hole, meas_type, meas_val) {	
					
			var url = "../lib/SRDb.php?dbcmnd=updateAxialHoleMeas&seg=" + $scope.seg +
					"&proc=" + $scope.proc + "&ver=" + $scope.ver + "&sec=" + $scope.sec + "&step=" + $scope.step +
					"&hole=" + hole + "&meas_type=" + meas_type + "&meas_val=" + meas_val;
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
		

		$timeout(function(){$scope.seg=$cookies.seg;}, 1000);
		$timeout(function(){$scope.update();}, 1100);
	}
]);
