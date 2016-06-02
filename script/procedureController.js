'use strict';

var srdbProcedureApp = angular.module('srdbProcedureApp', ['ngCookies']);

srdbProcedureApp.controller('ProcedureController', ['$scope', '$cookies', '$interval', 
		'$http', '$window', '$log', '$timeout', 
	
	function($scope, $cookies, $interval, $http, $window, $log, $timeout) {
			
		$scope.name = "ProcedureController";
		$scope.proc = "unknown";
		$scope.version = "unknown";
		$scope.seg = "unknown";
		$scope.segType = $cookies.seg_type;
		$scope.lotNum = $cookies.lot_num;
		$scope.segs = [];
		$scope.tech = "unknown";
		$scope.techNames = [];
		$scope.signOffData = [];
		$scope.notesInput = [];
		$scope.valueInput = [];
		$scope.signOffDesc = [];
		$scope.signOffList = [];
		$scope.signOffSummaryVisible = false;
		$scope.isBatchProc = false;	
		$scope.stopUpdating = false;
		$scope.content = "content";
		
		// get segment numbers
//		$http.get('../lib/SRDb.php?dbcmnd=segsjson').success(
//			function(data) {
//				$scope.segObjs = data;
//				for (var i = 0; i < $scope.segObjs.length; i++)
//					$scope.segs[i] = $scope.segObjs[i].sn;
//			}
//		);
		
		$scope.doOnLoad = function() {
			$scope.changeStyle($cookies.css_data_title);
		}
		
		$scope.changeStyle = function(title) {
			switch(title) {
				case "normal":
					$cookies.css_data_title = "normal";
					$window.styleSet("normal");
					break;
				default:
					$cookies.css_data_title = "large";
					$window.styleSet("large");
			}		
		}
		
		// get technicians
		$http.get('../lib/SRDb.php?dbcmnd=getTechsJSON').success(
			function(data) {
				$scope.techs = data;
				for (var i = 0; i < $scope.techs.length; i++)
					$scope.techNames[i] = $scope.techs[i].name;
			}
		);
			
		$scope.setSignOffDesc = function(proc, version, sec, step, type, label, valType, minVal, maxVal) {
//			$log.log("setSignOffDesc: proc=" + proc + "version=" + version + " sec=" + sec + 
//			" step=" + step + " type=" + type + " label=" + label);
			$scope.proc = proc;
			$scope.version = version;
			if (!$scope.signOffDesc[sec]) $scope.signOffDesc[sec] = [];
			$scope.signOffDesc[sec][step] = {
					"type": type, "label": label, "valType": valType, "minVal": minVal, "maxVal": maxVal};
			
		};
			
		// gets the list of all sign-offs for this procedure (not sign-off action records)
		// fields are id, proc, version, section, step, type, title, label
		$scope.getSignOffList = function() {
			var url = '../lib/SRDb.php?dbcmnd=getSignOffListJSON&proc=' 
						+ $scope.proc + '&ver=' + $scope.version;
			$http.get(url).success(
				function(data) {
					$scope.signOffList = data;
				}
			);
		};
		
		$timeout($scope.getSignOffList, 1000);
		
		// gets all sign-off action records for this procedure, version, segment
		$scope.getSignOffs = function() {
			if ($scope.isBatchProc)
				var url = '../lib/SRDb.php?dbcmnd=getSignOffsJSON&seg=' 
					+ $scope.lotNum + '&proc=' + $scope.proc + '&ver=' + $scope.version;
			else
				var url = '../lib/SRDb.php?dbcmnd=getSignOffsJSON&seg=' 
					+ $scope.seg + '&proc=' + $scope.proc + '&ver=' + $scope.version;
			$log.log(url);
			$http.get(url).success(
				function(data) {
					if (data.indexOf("error") >= 0) {
						// $window.alert(data);
						$log.log(data);
						$scope.stopUpdating = true;
						return;
					}
					//$log.log(data);
					$scope.signoffs = data;
					for (var sec = 0; sec < $scope.signOffData.length; sec++) {
						if ($scope.signOffData[sec])
							for (var step = 0; step < $scope.signOffData[sec].length; step++)
								$scope.signOffData[sec][step] = {"signedoff": false};
					}
							
					for (var i = 0; i < $scope.signoffs.length; i++) {
						if (!$scope.signOffData[$scope.signoffs[i].sec])
							$scope.signOffData[$scope.signoffs[i].sec] = [];
						$scope.signOffData[$scope.signoffs[i].sec][$scope.signoffs[i].step] = 
							{	"signedoff": true, 
								"step": $scope.signoffs[i].step,
								"timestamp": $scope.signoffs[i].timestamp, 
								"value": $scope.signoffs[i].value,
								"notes": $scope.signoffs[i].notes,
								"tech": $scope.signoffs[i].tech
							};
					}
				}
			)
		};

		// sign-off action
		$scope.signoffAction = function(sec, step) {
			
			var msg = "There are open sign-offs in this procedure!";
			msg += "\nDo you really want to close out this procedure?";
			
			if ($scope.signOffDesc[sec][step].type == "proc_close_out")
				if ($scope.signOffIsOutOfSequence(sec, step))
					if (!($window.confirm(msg)))
						return;
			
			// if this is a "choice" type, make sure a selection has been made from the drop-down
			if (!$scope.valueInput[sec]) $scope.valueInput[sec] = [];
			if (!$scope.valueInput[sec][step]) {
				if ($scope.signOffDesc[sec][step].valType == "Choice") 
					$window.alert("Please select " + $scope.signOffDesc[sec][step].label + 
						" before signing-off.");
				else
					$window.alert("Please enter " + $scope.signOffDesc[sec][step].label + 
						" before signing-off.");
				return;
			}
			
			// Make sure a tech has been selected
			if (!$scope.tech || ($scope.tech == 'unknown') || ($scope.tech =='')) {
				$window.alert("Please select a Tech before signing-off.");
				return;
			}
			
			// Make sure a segment s/n or lot/batch number is set.
			if ($scope.isBatchProc) {
				if (!$scope.lotNum || ($scope.lotNum == 'unknown') || ($scope.lotNum =='')) {
					$window.alert("Please select a Lot # before signing-off.");
					return;
				}
			}
			else {
				if (!$scope.seg || ($scope.seg == 'unknown') || ($scope.seg =='')) {
					$window.alert("Please select a Segment before signing-off.");
					return;
				}
			}
			
			// Make sure value is in range for Integer and Float sign-off types.
			switch ($scope.signOffDesc[sec][step].valType) {
				case  "Integer":
					if (isNaN(parseInt($scope.valueInput[sec][step]))) {
						$window.alert($scope.signOffDesc[sec][step].label + " entered is not an integer.");
						return;
					}
					if (parseInt($scope.valueInput[sec][step]) < 
							parseInt($scope.signOffDesc[sec][step].minVal)) {
						$window.alert($scope.signOffDesc[sec][step].label + 
							" out of range.\nMinimum allowed value is " + 
							$scope.signOffDesc[sec][step].minVal + ".");
						return;
					}
					if (parseInt($scope.valueInput[sec][step]) > 
							parseInt($scope.signOffDesc[sec][step].maxVal)) {
						$window.alert($scope.signOffDesc[sec][step].label + 
							" out of range.\nMaximum allowed value is " + 
							$scope.signOffDesc[sec][step].maxVal + ".");
						return;
					}
					break;
				case  "Float":
					if (isNaN(parseFloat($scope.valueInput[sec][step]))) {
						$window.alert($scope.signOffDesc[sec][step].label + 
						" entered is not a floating-point number.");
						return;
					}
					if (parseFloat($scope.valueInput[sec][step]) < 
							parseFloat($scope.signOffDesc[sec][step].minVal)) {
						$window.alert($scope.signOffDesc[sec][step].label + 
							" out of range.\nMinimum allowed value is " + 
							$scope.signOffDesc[sec][step].minVal + ".");
						return;
					}
					if (parseFloat($scope.valueInput[sec][step]) > 
							parseFloat($scope.signOffDesc[sec][step].maxVal)) {
						$window.alert($scope.signOffDesc[sec][step].label + 
							" out of range.\nMaximum allowed value is " + 
							$scope.signOffDesc[sec][step].maxVal + ".");
						return;
					}
					break;
			}
					
			// Set notes to "none" if no note has been entered.
			if (!$scope.notesInput[sec]) $scope.notesInput[sec] = [];
			if (!$scope.notesInput[sec][step]) $scope.notesInput[sec][step] = "none";

			// Enter sign-off action in database.
			if ($scope.isBatchProc)
				var url = "../lib/SRDb.php?dbcmnd=addSignOff&type=" + 
					$scope.signOffDesc[sec][step].type + 
					"&seg=" + $scope.lotNum + 
					"&tech=" + $scope.tech + 
					"&proc=" + $scope.proc + 
					"&ver=" + $scope.version + 
					"&sec=" + sec + "&step=" + step + 
					"&value=" + $scope.valueInput[sec][step].trim() + 
					"&notes=" + $scope.notesInput[sec][step].replace(/\&/g, '%26').replace(/\+/g, '%2B');
			else
				var url = "../lib/SRDb.php?dbcmnd=addSignOff&type=" + 
					$scope.signOffDesc[sec][step].type + 
					"&seg=" + $scope.seg + 
					"&tech=" + $scope.tech + 
					"&proc=" + $scope.proc + 
					"&ver=" + $scope.version + 
					"&sec=" + sec + "&step=" + step + 
					"&value=" + $scope.valueInput[sec][step].trim() + 
					"&notes=" + $scope.notesInput[sec][step].replace(/\&/g, '%26').replace(/\+/g, '%2B');
			
			//$log.log(url);
			
			$http.get(url).success(function(data) { 
				if (data.indexOf("error") >= 0)
					$window.alert(data) } );
		}
		
		$scope.signOffIsOutOfSequence = function(sec, step) {
			$log.log($scope.signOffData);
			$log.log("starting check for sec " + sec + " step " + step);
			var i, isec, istep;
			for (i = 0; i < $scope.signOffList.length; i++) {
				isec = $scope.signOffList[i].section;
				istep = $scope.signOffList[i].step;
				if ((isec < sec) || ((isec == sec) && (istep < step))) {
					$log.log("checking sec " + isec + " step " + istep);
					if ($scope.signOffData.length < 1)
						return(true);
					if (!$scope.signOffData[isec])
						return(true);
					if (!$scope.signOffData[isec][istep])
						return(true);
					if (!$scope.signOffData[isec][istep].signedoff)
						return(true);
				}
			}
			return(false);
		}

		
		$interval(function() { 
				
			if ($scope.stopUpdating)
				return;
							
			// update seg type from cookie
			$scope.segType = $cookies.seg_type;
			if (!$scope.segType)
				$scope.segType = "unknown";
				
			// update seg sn from cookie
			$scope.seg = $cookies.seg;
			if (!$scope.seg)
				$scope.seg = "unknown";
				
			// update tech from cookie
			$scope.tech = $cookies.tech;
			if (!$scope.tech)
				$scope.tech = "unknown";
				
			$scope.lotNum = $cookies.lot_num;
			if (!$scope.lotNum)
				$scope.lotNum = "unknown";
				
			// get list of sign-offs for this procedure
			// why do this more than once since will not change once procedure is loaded
			//$scope.getSignOffList();
			
			// get list of sign-off actions for this procedure and segment/batch
			$scope.getSignOffs();
			
			$scope.content = $cookies.content;
			if (!$scope.content)
				$scope.content = "content";
			

		}, 500);
		
		// cannot change segment from procedure page
		//$scope.segChanged = function() {
		//	$cookies.seg = $scope.seg;
		//}
		
		$scope.techChanged = function() {
			$cookies.tech = $scope.tech;
		}
		
		$scope.setSignOffSummaryVisible = function(visible) {
			//$log.log("setSignOffSummaryVisible " + visible);
			$scope.signOffSummaryVisible = visible;
		}
		
		$scope.openDataEntryTable = function(baseUrl, proc, ver, sec, step) {
			//$window.alert(baseUrl);
			var url = baseUrl;
			if (baseUrl.indexOf('?') < 0)
				url += '?';
			else
				url += '&';
			$window.open(url + "seg=" + $scope.seg + "&proc=" + proc + "&ver=" + ver +
					"&sec=" + sec + "&step=" + step);	
		}
		
		$scope.openNCR = function(ip) {
			var s = '&type=' + $scope.segType + '&seg=' + $scope.seg;
			if ($scope.isBatchProc)
				//s = '&lot=' + $scope.lotNum;
				s = '&type=0&seg=' + $scope.lotNum;
			var url = "https://www.keck.hawaii.edu/optics/segrepair/db/pages/ncr.php?"+ 
				"ip=" + ip + "&proc=" + $scope.proc + s;
//			var url = "https://www.keck.hawaii.edu/sandbox/jmader/ncr/ncr.php?"+ 
//				"ip=" + ip + "&proc=" + $scope.proc + "&type=" + $scope.segType + "&seg=" + $scope.seg;
			$log.log(url);
			$window.open(url, '_blank');
		}
		
		// notes stuff
		
		$scope.showNewNoteArea = false;
		$scope.newNoteText = "";
		$scope.notes = [];
		$scope.maxNoteLength = 256;
		
		$scope.getNotes = function() {
			var url;
			if ($scope.isBatchProc)
				url = '../lib/SRDb.php?dbcmnd=getProcNotesJSON&proc=' + $scope.proc +
					'&ver=' + $scope.version + '&seg=' + $scope.lotNum;
			else
				url = '../lib/SRDb.php?dbcmnd=getProcNotesJSON&proc=' + $scope.proc +
					'&ver=' + $scope.version + '&seg=' + $scope.seg;
			$http.get(url).success(
				function(data) {
					//$log.log(data);
					$scope.notes = data;
				}
			);		
		}
		
		$scope.newNote = function() {
			$scope.showNewNoteArea = true;
		}
		
		$scope.noteTextChanged = function() {
			//$log.log($scope.newNoteText.length + '/' + $scope.maxNoteLength);
			if ($scope.newNoteText.length > $scope.maxNoteLength) {
				$window.alert('Maximum note text length exceeded.');
				$scope.newNoteText = $scope.newNoteText.substring(0, $scope.maxNoteLength);
			}	
		}
		
		$scope.save = function() {
			if ($scope.tech == "unknown" || $scope.tech.length < 2) {
				$window.alert("Please select a tech before adding a note.");
				return;
			}
							
			if ($scope.isBatchProc) {
				if ($scope.lotNum == "unknown" || $scope.lotNum.length < 2) {
					$window.alert("Please select a lot # before adding a note.");
					return;
				}
			}
			else {
				if ($scope.seg == "unknown" || $scope.seg.length < 2) {
					$window.alert("Please select a segment before adding a note.");
					return;
				}
			}
			
			var url;
			if ($scope.isBatchProc)
				url = "../lib/SRDb.php?dbcmnd=addProcNote&seg=" + $scope.lotNum +
					"&proc=" + $scope.proc + "&ver=" + $scope.version + 
					"&tech=" + $scope.tech + "&note=" + $scope.newNoteText.replace(/\&/g, '%26').replace(/\+/g, '%2B');
			else
				url = "../lib/SRDb.php?dbcmnd=addProcNote&seg=" + $scope.seg +
					"&proc=" + $scope.proc + "&ver=" + $scope.version + 
					"&tech=" + $scope.tech + "&note=" + $scope.newNoteText.replace(/\&/g, '%26').replace(/\+/g, '%2B');

			url = url.replace(/[\n]/g, '<br />');
			url = url.replace(/[\s]/g, '%20');
			url = url.replace(/[#]/g, '%23');
			url = url.replace(/\"/g, '%22');
			url = url.replace(/\'/g, '%27');

			//$log.log(url);
			$http.get(url).success(
				function(data) {
					//$scope.inedit[pos] = false;
					//$scope.update();	
					$scope.newNoteText = "";			
				}
			);
			$scope.showNewNoteArea = false;
			$scope.getNotes();
		}
				
		$scope.cancel = function() {
			$scope.newNoteText = "";
			$scope.showNewNoteArea = false;
		}
				
	$interval(function() {
		$scope.getNotes();
	}, 1000);
	
	
	}
]);

srdbProcedureApp.filter('unsafe', function($sce) {
	return function(val) {
		return $sce.trustAsHtml(val);
	};
});