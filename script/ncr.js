(function() {
	var app = angular.module("ncr", []);
	app.controller("ncr", ["$scope", "$http", function($scope, $http) {
		var list = this;
		//
		// Security information
		//
		list.tech = "please login";
		list.isloggedin = 0;
		list.isadmin = 0;
		list.canEditIPs = 0;
		list.canApproveQuality = 0;
		list.canApproveEngineering = 0;
		list.canApproveRework = 0;
		list.canAddCorrAction = 0;
		list.canCloseCorrAction = 0;
		list.canAddLimProcess = 0;
		//
		// Controls different page views
		//
		list.newNCR = 0;
		list.listIPs = 0;
		list.listCAs = 0;
		list.editIP = 0;
		list.editNCR = 0;
		list.listNCR = 1;
		list.entry = [];
		list.entries = [];
		list.readonly = 0;
		list.isSeg = 0;
		list.isLot = 0;
		list.ca = [];
		//
		// INIT to set form entries from GET variables
		//
		$scope.init = function() {
			$scope.getTech();
			var ncr = getUrlVars("ncr");
			if (ncr) {
				list.editNCR = 1;
				list.listNCR = 0;
				$scope.user = [];
				$scope.user.ncrno = ncr;
				$scope.setNcrEntry($scope.user);
			} else {
				var ip = getUrlVars("ip");
				var proc = getUrlVars("proc");
				var seg_type = getUrlVars("type");
				var seg_sn = getUrlVars("seg");
				var com_lot = getUrlVars("lot");
				if (seg_type) {
					list.newNCR = 1;
					list.listNCR = 0;
					$scope.user = [];
					$scope.user.seg_type = seg_type;
					$scope.getSegSN();
					$scope.user.seg_sn = seg_sn;
					$scope.user.plan_num = ip;
					$scope.getViolation();
					$scope.user.part_description = "Mirror Segment";
					list.isSeg = 1;
				}
				else if (com_lot) {
					list.newNCR = 1;
					list.listNCR = 0;
					$scope.user = [];
					$scope.user.plan_num = ip;
					$scope.getViolation();
					$scope.user.com_lot = com_lot;
					list.isLot = 1;
				}
			}
		};
		//
		// Which tech?
		//
		$scope.getTech = function() {
			list.isloggedin = 0;
			list.isadmin = 0;
			list.canEditIPs = 0;
			list.canApproveQuality = 0;
			list.canApproveEngineering = 0;
			list.canApproveRework = 0;
			list.canAddCorrAction = 0;
			list.canCloseCorrAction = 0;
			list.canAddLimProcess = 0;
			var name = "WMKOuser=";
			var ca = document.cookie.split(";");
			for (var i = 0 ; i < ca.length ; i++) {
				var c = ca[i].trim();
				if (c.indexOf(name) == 0) {
					list.tech = c.substring(name.length, c.length);
					list.isloggedin = 1;
					//
					// Page views change based on who is logged in
					//
					if (list.tech == "jmader") {
						list.isadmin = 1;
						list.canEditIPs = 1;
						list.canApproveQuality = 1;
						list.canApproveEngineering = 1;
						list.canApproveRework = 1;
						list.canAddCorrAction = 1;
						list.canCloseCorrAction = 1;
						list.canAddLimProcess = 1;
					}
					if (list.tech == "lwold") {
						list.isadmin = 1;
						list.canEditIPs = 1;
						list.canApproveQuality = 1;
						list.canApproveRework = 1;
						list.canAddCorrAction = 1;
						list.canCloseCorrAction = 1;
						list.canAddLimProcess = 1;
					}
					if (list.tech == "twold") {
						list.isadmin = 1;
						list.canApproveEngineering = 1;
					}
					if (list.tech == "dmcbride") {
						list.isadmin = 1;
						list.canApproveEngineering = 1;
						list.canAddCorrAction = 1;
						list.canAddLimProcess = 1;
					}
				}
			}
			if (list.isloggedin) return;
			//
			// Use pulldown selection cookie - limited views
			//
			var name = "tech=";
			var ca = document.cookie.split(";");
			for (var i = 0 ; i < ca.length ; i++) {
				var c = ca[i].trim();
				if (c.indexOf(name) == 0)
					list.tech = c.substring(name.length, c.length);
			}
		};
		//
		// For sorting tables
		// 
		$scope.predicate = "ncr";
		$scope.reverse = "true";
		$scope.order = function(predicate) {
			$scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
			$scope.predicate = predicate;
		};
		//
		// Update the page to display an empty form for submission
		//
		$scope.newNCR = function() {
			if (!list.isloggedin) {
				alert("Please login");
				return;
			}
			$scope.user = [];
			$scope.user.quantity = 1;
			list.newNCR = 1;
			list.listIPs = 0;
			list.listCAs = 0;
			list.editIP = 0;
			list.editNCR = 0;
			list.listNCR = 0;
			list.readonly = 0;
		};
		//
		// Update the page to display all IP's
		//
		$scope.listIPs = function() {
			$scope.user = [];
			list.newNCR = 0;
			list.listIPs = 1;
			list.listCAs = 0;
			list.editIP = 0;
			list.editNCR = 0;
			list.listNCR = 0;
			list.readonly = 0;
			user = [];
			$scope.predicate = "ip";
			$http.get("../lib/ncr/getAllIPs.php").success(function(data) {
				list.entries = data;
			});
		};
		//
		// Update the page to display all corrective actions
		//
		$scope.listCAs = function(type, user="") {
			$scope.user = [];
			list.newNCR = 0;
			list.listIPs = 0;
			list.listCAs = 1;
			list.editIP = 0;
			list.editNCR = 0;
			list.listNCR = 0;
			list.readonly = 0;
			$scope.predicate = "due_date";
			if (user) type = "search&str="+user.search;
			$http.get("../lib/ncr/getAllCAs.php?type="+type).success(function(data) {
				list.entries = data;
			});
		};
		//
		// Update the page to edit an NCR
		//
		$scope.editNCR = function(user) {
			if (!list.isloggedin) {
				alert("Please login");
				return;
			}
			list.newNCR = 0;
			list.listIPs = 0;
			list.listCAs = 0;
			list.editIP = 0;
			list.listNCR = 0;
			list.readonly = 0;
			if (!user) return;
			list.editNCR = 1;
			$scope.getTech();
			$scope.setNcrEntry(user);
		};
		//
		// Update the page to edit an NCR
		// Called from ncr-list.html
		//
		$scope.editNCR2 = function(ncrno) {
			if (!list.isloggedin) {
				alert("Please login");
				return;
			}
			list.newNCR = 0;
			list.listIPs = 0;
			list.listCAs = 0;
			list.editIP = 0;
			list.listNCR = 0;
			list.readonly = 0;
			if (!ncrno) return;
			list.editNCR = 1;
			$scope.user = [];
			$scope.getTech();
			$scope.user.ncrno = ncrno;
			$scope.setNcrEntry($scope.user);
		};
		//
		// Update the page to list all submitted NCRs
		//
		$scope.listNCR = function() {
			list.newNCR = 0;
			list.listIPs = 0;
			list.listCAs = 0;
			list.editIP = 0;
			list.editNCR = 0;
			list.listNCR = 1;
			$scope.predicate = "ncr";
			user = [];
			$http.get("../lib/ncr/getAllNcr.php").success(function(data) {
				list.entries = data;
			});
		};
		//
		// Filter the NCR list
		//
		$scope.filterNcrList = function(user) {
			$http.get("../lib/ncr/getAllNcr.php", {
				params: {
						seg_type: user.seg_type,
						seg_sn: user.seg_sn2,
						stat: user.status
					}
			}).success(function(data) {
				list.entries = data;
			});
		};
		//
		// Update inspection plan
		// Called from ncr-ip.html
		//
		$scope.editIP = function(ip) {
			if (!list.canEditIPs)
			{
				alert("You do not have permission to edit this IP");
				return;
			}
			list.newNCR = 0;
			list.listIPs = 1;
			list.listCAs = 0;
			list.listNCR = 0;
			list.editNCR = 0;
			list.readonly = 0;
			if (!ip) return;
			list.editIP = 1;
			$scope.user = [];
			$scope.user.ip = ip;
			$scope.getTech();
			$scope.setIPEntry($scope.user);
		};
		//
		// Add a new inspection plan
		// Called from ncr-ip.html
		//
		$scope.addNewIP = function() {
			$scope.getTech();
			$scope.predicate = "ip";
			if (!list.canEditIPs)
			{
				alert("You do not have permission to add IPs");
				return;
			}
			list.newNCR = 0;
			list.newNCR = 0;
			list.listIPs = 1;
			list.listCAs = 0;
			list.listNCR = 0;
			list.editNCR = 0;
			list.readonly = 0;
			list.editIP = 1;
			$scope.user = [];
		};
		//
		// Default: list all NCR's
		//
		$scope.listNCR();
		//
		// Returns whether or not the page is active
		//
		$scope.showNewNCR = function() {
			return list.newNCR;
		};
		$scope.showIPlist = function() {
			return list.listIPs;
		};
		$scope.showCAlist = function() {
			return list.listCAs;
		};
		$scope.showEditIP = function() {
			return list.editIP;
		};
		$scope.showEditNCR = function() {
			return list.editNCR;
		};
		$scope.showAllNCR = function() {
			return list.listNCR;
		};
		$scope.isReadOnly = function() {
			return list.readonly;
		};
		$scope.isSeg = function() {
			return list.isSeg;
		};
		$scope.isLot = function() {
			return list.isLot;
		};
		//
		// Returns the segment s/n for the give segment type
		//
		$scope.getSegSN = function() {
			seg_type = $scope.user.seg_type;
			$http.get("../lib/ncr/getSegSN.php?seg="+seg_type).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					$scope.seg_sn2 = data;
				}
			});
		};
		//
		// Returns the component description based on the lot
		//
		$scope.getComponentDescription = function() {
			pn = $scope.user.com_pn;
			$http.get("../lib/ncr/getComponentDescription.php?pn="+pn).success(function(data) {
				$scope.user.part_description = data;
			});
		};
		//
		// Sends the segment s/n to the form
		//
		$scope.setSegSN = function() {
			$scope.user.seg_sn = $scope.user.seg_sn2.value;
		};
		//
		// Returns the segment s/n for the give segment type
		//
		$scope.getViolation = function() {
			ip = $scope.user.plan_num;
			$http.get("../lib/ncr/getIpDescription.php?ip="+ip).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					$scope.user.violation = data.description;
				}
			});
		};
		//
		// Updates the form for the given NCR #
		//
		$scope.setNcrEntry = function(user) {
				$scope.getTech();
				$http.get("../lib/ncr/getNcr.php?ncr="+user.ncrno).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					list.entry = data;
					$scope.user.ncr = list.entry.ncr;
					$scope.user.seg_type = list.entry.seg_type;
					$scope.user.seg_sn = list.entry.seg_sn;
					$scope.getSegSN();
					$scope.user.com_pn = list.entry.com_pn;
					$scope.user.com_lot = list.entry.com_lot;
					$scope.user.quantity = list.entry.quantity;
					$scope.user.date_start = list.entry.date_start;
					$scope.user.date_end = list.entry.date_end;
					$scope.user.is_condition = list.entry.is_condition;
					$scope.user.disposition = list.entry.disposition;
					$scope.user.cause = list.entry.cause;
					$scope.user.violation = list.entry.violation;
					$scope.user.keyword = list.entry.keyword;
					$scope.user.plan_num = list.entry.plan_num;
					if ($scope.user.date_end) list.readonly = 1;
					$scope.getViolation();
					$scope.updateLimitedProcess($scope.user.ncr);
					$scope.updateCorrAction($scope.user.ncr);
					$scope.updateApproval($scope.user.ncr);
					$scope.getAttachments(list.entry.ncr);
				}
			});
		};
		//
		// Submit entries to the database (insert or update)
		//
		$scope.updateNcr = function(user) {
			if (!user.ncr) user.ncr = 0;
			$http.get("../lib/ncr/updateNcr.php", {
				params: {
						ncr: user.ncr,
						seg_type: user.seg_type,
						seg_sn: user.seg_sn,
						com_pn: user.com_pn,
						com_lot: user.com_lot,
						quantity: user.quantity,
						is_condition: user.is_condition,
						disposition: user.disposition,
						cause: user.cause,
						keyword: user.keyword,
						plan_num: user.plan_num,
						date_start: user.date_start,
						username: list.tech
					}
			}).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					alert("NCR Submitted");
					$scope.editNCR2(data.ncrno);
				}
			});
		};
		//
		// Adds a corrective action entry to the NCR
		// ncr-form.html
		//
		$scope.addCorrAction = function(user) {
			$http.get("../lib/ncr/addCorrAction.php", {
				params: {
						ncr: user.ncr,
						assign_to: user.ca_name,
						due_date: user.ca_date,
						action: user.corrective_newaction,
						username: list.tech
					}
			}).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					$scope.user.ca_name = "";
					$scope.user.ca_date = "";
					$scope.user.corrective_newaction = "";
					$scope.updateCorrAction(data.ncr);
				}
			});
		};
		//
		// Closes a corrective action entry for the NCR
		// ncr-form.html
		//
		$scope.closeCorrAction = function(id) {
			if (!id) {
				alert("Unknown ID");
				return;
			}
			$http.get("../lib/ncr/closeCorrAction.php", {
				params: {
						id: id,
						username: list.tech
				}
			}).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					$scope.updateCorrAction(data.ncr);
				}
			});
		};
		//
		// Edit a corrective action entry for the NCR
		// ncr-form.html
		//
		$scope.updateCorrAction = function(ncrno) {
			list.ca = [];
			$http.get("../lib/ncr/getCorrAction.php", {
				params: {
						ncr: ncrno
				}
			}).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					list.ca = data;
					$scope.user.corrective_action = data.corrective_action;
				}
			});
		};
		//
		// Submit a new limited process entry
		// ncr-form.html
		//
		$scope.addLimitedProcess = function(user) {
			$http.get("../lib/ncr/addLimitedProcess.php", {
				params: {
						ncr: user.ncr,
						limited_process: user.limited_process_text,
						username: user.limited_process_username //list.tech
					}
			}).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					$scope.user.limited_process_text = "";
					$scope.user.limited_process_username = "";
					$scope.updateLimitedProcess(user.ncr);
				}
			});
		};
		//
		// Update a limited process entry
		// ncr-form.html
		//
		$scope.updateLimitedProcess = function(ncrno) {
			$http.get("../lib/ncr/getLimitedProcess.php", {
				params: {
						ncr: ncrno
				}
			}).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					$scope.user.limited_process = data.limited_process;
					if (list.isloggedin) $scope.user.limited_process_username = list.tech;
				}
			});
		};
		//
		// Submit a new approval for an NCR
		// Restricted to certain users
		// ncr-form.html
		//
		$scope.addApproval = function(user) {
			if ((user.da_type == "Engineering" && !list.canApproveEngineering) ||
			    (user.da_type == "Quality" && !list.canApproveQuality) ||
			    (user.da_type == "Rework" && !list.canApproveRework)) {
				alert("You do not have permission to make this approval");
				return;
			}
			$http.get("../lib/ncr/addApproval.php", {
				params: {
						ncr: user.ncr,
						type: user.da_type,
						username: user.da_name
					}
			}).success(function(data) {
				if (data.error) {
					alert(data.error);
				}
				else {
					$scope.setNcrEntry(user);
					$scope.updateApproval(data.ncr);
				}
			});
		};
		//
		// Update the approval section of the NCR
		// ncr-form.html
		//
		$scope.updateApproval = function(ncrno) {
			$http.get("../lib/ncr/getApprovals.php", {
				params: {
						ncr: ncrno
				}
			}).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					$scope.user.da_type = "";
					$scope.user.da_name = "";
					if (data.Engineering)
						$scope.user.da_eng = data.Engineering;
					if (data.Quality)
						$scope.user.da_quality = data.Quality;
					if (data.Rework)
						$scope.user.da_rework = data.Rework;
					if (list.isloggedin) $scope.user.da_name = list.tech;
				}
			});
		};
		//
		// Get the desired inspection plan
		//
		$scope.setIPEntry = function(user) {
				$http.get("../lib/ncr/getIP.php?ip="+user.ip).success(function(data) {
				if (data.error) {
					alert(data.error);
				} else {
					list.entry = data;
					$scope.user.description = list.entry.description;
				}
			});
		};
		//
		// Submit entries to the database (insert or update)
		// Called from ncr-ip.html
		//
		$scope.updateIP = function(user) {
			if (!user.ip) user.ip = 0;
			$http.get("../lib/ncr/updateIP.php", {
				params: {
						ip: user.ip,
						description: user.description,
						username: list.tech
					}
			}).success(function(data) {
				if (data.error) {
					alert(data.error);
					console.log(data.error);
				} else {
					alert("IP Submitted");
					console.log("IP Submitted");
					$scope.user.ip = data.ip;
					//console.log(data.query);
					//console.log(data.ip);
				}
			});
		//
		// Upload a file and attach to this NCR
		//
		};
		$scope.getAttachments = function(ncr) {
			$http.get("../lib/ncr/getAttachments.php?ncr="+ncr).success(function(data) {
				if (data.error) {
					alert(data.error);
					list.attachments = [];
				} else {
					list.attachments = data.files;
				}
			});
		};
	}]);
	//
	// Directives used in ncr.php
	//
	app.directive("ncrForm", function() {
		return {
			restrict: "E",
			templateUrl: "../directive/ncr-form.html",
			controller: function() {
			},
			controllerAs: "ncr"
		};
	});
	app.directive("ncrIp", function($http) {
		return {
			restrict: "E",
			templateUrl: "../directive/ncr-ip.html",
			controller: function() {
			},
			controllerAs: "iplist"
		};
	});
	app.directive("ncrCorraction", function($http) {
		return {
			restrict: "E",
			templateUrl: "../directive/ncr-corraction.html",
			controller: function() {
			},
			controllerAs: "calist"
		};
	});
	app.directive("ncrList", function($http) {
		return {
			restrict: "E",
			templateUrl: "../directive/ncr-list.html",
			controller: function() {
			},
			controllerAs: "listall"
		};
	});
})();

function getUrlVars(name)
{
	url = window.location.href;
	name = name.replace(/[\[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"), 
	    results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return "";
	return decodeURIComponent(results[2].replace(/\+/g, " "));
}
