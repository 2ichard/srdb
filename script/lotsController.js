'use strict';

var srdbLotsApp = angular.module('srdbLotsApp', []);

srdbLotsApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbLotsApp.controller('LotsCtrl', ['$scope', '$http', '$log', '$window', '$interval',

	function($scope, $http, $log, $window, $interval) {
	
		$scope.name = 'LotsCtrl';
		$scope.lots = [];
		$scope.orderProp = "id";
		$scope.inedit = [];
		$scope.lotTypes = ['axial_inserts', 'etch_soln', 'flex_rods', 'radial_pads', ]
				
		$scope.getLotById = function(id) {
			for (var i = 0; i < $scope.lots.length; i++)
				if (id == $scope.lots[i].id) 
					return($scope.lots[i]);
			return(null);
		}
		
		$scope.edit = function(id) {
			for (var i = 0; i < $scope.inedit.length; i++)
				if ($scope.inedit[i] == true) {
					$window.alert("Finish edit already in progress first.");
					return;
				}
			$scope.inedit[id] = true;
			$log.log("editing id " + id);
			$log.log($scope.getLotById(id));
		}
		
		$scope.unedit = function(id) {
			$scope.inedit[id] = false;
			if (id == 0)
				$scope.lots.pop();
			$scope.update();
		}
		
		$scope.createNew = function() {
			for (var i = 0; i < $scope.lots.length; i++) {
				if ($scope.inedit[$scope.lots[i].id]) {
					$window.alert("Finsih editing first.");
					return;
				}
			}
			$scope.lots.push({"id": 0, "type": "", "q_init": 0, "q_now": 0, "status": "Inactive"});
			$scope.inedit[0] = true;
		}
		
		$scope.save = function(id) {
			
			var lot = $scope.getLotById(id);
			
			if (lot == null) {
				$log.log("ERROR: invalid id");
				return;
			}
			
			if (id == 0) {
				// new
				if (lot.type.length < 1) {
					$window.alert("Please select type.");
					return;
				}
			
				var url = "../lib/SRDb.php?dbcmnd=addLot&type=" + lot.type + "&num=" + lot.lot;
				url = url.replace(/[\s]/g, '%20');
				url = url.replace(/[#]/g, '%23');
				$log.log(url);
				$http.get(url).success(
					function(data) {
						if (data.indexOf("error") >= 0) {
							$window.alert(data)
							return;
						}
						$scope.lots.pop();
						$scope.inedit[0] = false;
						$scope.update();
					}
				);
			}
			else {
				$http.get("../lib/SRDb.php?dbcmnd=updateLot&lot=" + lot.lot + "&what=q_init&val=" + lot.q_init);
				$http.get("../lib/SRDb.php?dbcmnd=updateLot&lot=" + lot.lot + "&what=q_now&val=" + lot.q_now);
				var url = "../lib/SRDb.php?dbcmnd=updateLot&lot=" + lot.lot + "&what=status&val=" + lot.status;
				$http.get(url).success( 
					function(data) {
						if (data.indexOf("error") >= 0) {
							$window.alert(data)
							return;
						}
					}
				);
				
//					$http.get("../lib/SRDb.php?dbcmnd=updateLot&lot=" + lot.log + 
//						"&what=q_now&val=" + lot.q_now);
//					$http.get("../lib/SRDb.php?dbcmnd=updateLot&lot=" + lot.log + 
//						"&what=active&val=" + lot.active);
				$scope.inedit[id] = false;
			}
		}
		
		$scope.delete = function(id) {
			$http.get("../lib/SRDb.php?dbcmnd=deleteLot&id=" + id).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					$scope.update();
					$scope.inedit[id] = false;
				}
			);
		}
			
		$scope.genLotNo = function() {
			var lot = $scope.getLotById(0);
			$http.get('../lib/SRDb.php?dbcmnd=getNextLotJSON').success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					lot.lot = data[0].lot;
				}
			);
		}
		
		$scope.update = function() {
			$http.get('../lib/SRDb.php?dbcmnd=getLotsJSON&type=all').success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					$scope.lots = data;
					for (var lot in $scope.lots) 
						$scope.inedit[lot.id] = false
				}
			);
		}
		
		$scope.update();
//		$interval(function() {
//			if ($scope.inedit[0])
//				return;
//			for (var lot in $scope.lots)
//				if ($scope.inedit[lot.id])
//					return;
//			$scope.update();
//		}, 1000);
	}
]);
