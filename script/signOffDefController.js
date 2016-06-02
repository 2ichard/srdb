'use strict';

var srdbSignOffDefApp = angular.module('srdbSignOffDefApp', []);

srdbSignOffDefApp.config(['$compileProvider', function($compileProvider) {
  $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|file|javascript):/);
}]);

srdbSignOffDefApp.controller('SignOffListCtrl', ['$scope', '$http', '$log', '$window',

	function($scope, $http, $log, $window) {
	
		$scope.name = 'SignOffListCtrl';
		$scope.desc = [];
		$scope.modInProgress = false;
		$scope.orderProp = "id";
		$scope.inedit = [];
		
		$scope.edit = function(id) {
			$scope.inedit[id] = true;
		}
		
		$scope.unedit = function(id) {
			$scope.inedit[id] = false;
			if (id == 0)
				$scope.desc.pop();
			$scope.update();
		}
		
		$scope.createNew = function() {
			for (var i = 0; i < $scope.desc.length; i++) {
				if ($scope.inedit[$scope.desc[i].id]) {
					$window.alert("Finsih editing first.");
					return;
				}
			}
			$scope.desc.push({"id": 0, "type": "", "title": "", "label": "", "valType": "", 
					"minVal": "", "maxVal": "", "choices": ""});
			$scope.inedit[0] = true;
		}
		
		$scope.save = function(id) {
			$log.log("save id = " + id);
			var d = [];
			var i;
			for (i = 0; i < $scope.desc.length; i++) {
				if ($scope.desc[i].id == id) {
					d = $scope.desc[i];
					break;
				}
			}
			
			if (i == $scope.desc.length) {
				$log.log("ERROR: invalid id");
				return;
			}
			
			if (d.type.length < 1) {
				$window.alert("Please enter a name for sign-off type.");
				return;
			}
			if (d.title.length < 1) {
				$window.alert("Please enter a title.");
				return;
			}
			if (d.valType.length < 1) {
				$window.alert("Please select a value type.");
				return;
			}
			
			switch (d.valType) { 
				case "Choice":
					if (d.minVal.length > 0 || d.maxVal.length > 0) 
						$window.alert("Min and max values will be ignored because value type is Choice.");
					else {
						d.minVal = "null";
						d.maxVal = "null";
					}
					if (d.choices.length <= 0)
						d.choices = "null";
					break;
					
				case "String":
					if (d.minVal.length > 0 || d.maxVal.length > 0) 
						$window.alert("Min and max values will be ignored because value type is String.");
					d.minVal = "null";
					d.maxVal = "null";
					if (d.choices.length > 0 && d.choices != "null") 
						$window.alert("Choices will be ignored because value type is String.");
					d.choices = "null";
					break;
					
				case "Integer":
					if (d.minVal.length <= 0) {
						$window.alert("Please specify a minimum allowed integer value.");
						return;
					}
					if (isNaN(parseInt(d.minVal))) {
						$window.alert("Min value is not a valid integer.");
						return;
					}
					if (isNaN(parseInt(d.maxVal))) {
						$window.alert("Max value is not a valid integer.");
						return;
					}
					if (d.choices.length > 0 && d.choices != "null") 
						$window.alert("Choices will be ignored because value type is Integer.");
					d.choices = "null";
					break;
					
				case "Float":
					if (d.minVal.length <= 0) {
						$window.alert("Please specify a minimum allowed floating point value.");
						return;
					}
					if (isNaN(parseFloat(d.minVal))) {
						$window.alert("Min value is not a valid floating point number.");
						return;
					}
					if (isNaN(parseFloat(d.maxVal))) {
						$window.alert("Max value is not a valid floating point value.");
						return;
					}
					if (d.choices.length > 0 && d.choices != "null") 
						$window.alert("Choices will be ignored because value type is Float.");
					d.choices = "null";
					break;
			}
				
			var url = "../lib/SRDb.php?dbcmnd=addSignOffDesc&id=" + d.id + "&type=" + d.type + 
					"&title=" + d.title + "&label=" + d.label + "&valType=" + d.valType + 
					"&minVal=" + d.minVal + "&maxVal=" + d.maxVal + "&choices=" + d.choices;
			url = url.replace(/[\s]/g, '%20');
			url = url.replace(/[#]/g, '%23');
			//$log.log(url);
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					if (d.id == 0) $scope.desc.pop();
					$scope.inedit[d.id] = false;
					$scope.update();
				}
			);
		}
		
		$scope.delete = function(id) {
			$http.get("../lib/SRDb.php?dbcmnd=deleteSignOffDesc&id=" + id).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					$scope.update();
				}
			);
		}
		
		$scope.update = function() {
			$http.get('../lib/SRDb.php?dbcmnd=getSignOffDescJSON&type=all').success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						$window.alert(data)
						return;
					}
					//$log.log(data);
					$scope.desc = data;
					for (var $d in $scope.desc)
						$scope.inedit[$d.id] = false;
				}
			);
		}
		
		$scope.update();
	}
]);
