<!DOCTYPE html>
<!--------------------------------------------------------------------->
<!-- Verify login                                                    -->
<!--------------------------------------------------------------------->
<?php
if ($_GET["login"] == "yes")
{
	include "/home/www/public/commonMenus2/authentication.inc";
	$wmkoportal = new authentication("bypass");
	if (!$wmkoportal->loggedin)
		return;
}
?>
<html ng-app="ncr">
<head>
	<title>SRP -NCR</title>
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/srdb.css">
	<link rel="stylesheet" type="text/css" href="../css/ncr.css">
	<script type="text/javascript" src="../script/angular.js"></script>
	<script type="text/javascript" src="../script/ncr.js"></script>
</head>
<body id="grad6" ng-controller="ncr as report" ng-init="init()">
	<!------------------------------------------------------------->
	<!-- Verify login                                            -->
	<!------------------------------------------------------------->
	<p ng-show="report.isloggedin">&nbsp;logged in as "{{report.tech}}"</p>
	<form ng-show="!report.isloggedin" action="ncr.php?login=yes">
		<input type="hidden" name="login" value="yes">
		<input type="submit" value="Login">
	</form>
	<!------------------------------------------------------------->
	<!-- Header and buttons available on all pages               -->
	<!------------------------------------------------------------->
	<header class="header">
	<h1 class="text-center">Segment Repair Project</h1>
	</header>
	<center>
	<h4>Tech: {{report.tech}}</h4>
	<button type='submit' ng-click='listNCR()'>List all NCR's</button>&nbsp;&nbsp;&nbsp;
	<button type='submit' ng-click='newNCR()'>Submit a new report</button>&nbsp;&nbsp;&nbsp;
	<button type='submit' ng-click='editNCR(user)'>Edit report</button>
	<input type='text' ng-model='user.ncrno' size="3"></input></br></br>
	<button type='submit' ng-show="showIPlist() != 1" ng-click='listIPs()'>List all IP's</button>&nbsp;&nbsp;&nbsp;
	<button type='submit' ng-show="showCAlist() != 1 && report.canAddCorrAction" ng-click='listCAs()'>Show all Corrective Actions</button>&nbsp;&nbsp;&nbsp;
	<!------------------------------------------------------------->
	<!-- Directives                                              -->
	<!------------------------------------------------------------->
	<div ng-show='showNewNCR() == 1'>
		</br>
		<ncr-form></ncr-form>
	</div>
	<div ng-show='showEditNCR() == 1'>
		</br>
		<ncr-form></ncr-form>
	</div>
	<div ng-show='showAllNCR() == 1'>
		</br>
		<ncr-list></ncr-list>
	</div>
	<div ng-show='showIPlist() == 1'>
		</br>
		<ncr-ip></ncr-ip>
	</div>
	<div ng-show='showCAlist() == 1'>
		</br>
		<ncr-corraction></ncr-corraction>
	</div>
	</center>
</body>
</html>
