<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script src="../script/angular.js"></script>
<script src="../script/techTableController.js"></script>

<link rel="stylesheet" type="text/css" href="../css/srdb.css" /><style type="text/css">
.kindoftiny {
	font-size: 9px;
	color: #999;
}
</style>
<title>Tech Table</title>
</head>

<body id="grad6" ng-app="srdbTechTableApp">
<form id="form1" name="form1" method="post" action="">
<table width="95%" align="center" border="1" cellspacing="0" cellpadding="5" bordercolor="#DDDDDD" ng-controller="TechTableCtrl">
	<tr>
		<td align="center">
		
		<!-- header table -->
		<table width="95%" border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td><span class="title1"><span class="title1_not_bold">
				<a href="http://www.keck.hawaii.edu/optics/segrepair/dev/db/index.html">
				Segment Repair Database</a> 
				</span></span></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
			<tr>
				<td align="left" class="modeind"><span class="title1">Tech Table</span></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
		</table>
		<!-- end header table -->
		
		</td>
	</tr>
	
	<tr >
		<td align="center">
			
			<table border="0" cellspacing="0" cellpadding="2" bordercolor="#CCCCCC">
	<tr >
		<td><table border="0" cellspacing="2" cellpadding="2" bordercolor="#CCCCCC">
			<tr>
				<td align="center" nowrap="nowrap">Techs</td>
			</tr>
			<tr>
				<td align="center">
				<table border="1" bordercolor="#CCCCCC" align="center" cellpadding="2" cellspacing="0">
					<tr ng-repeat="tech in techs | orderBy : 'tech.name'">
						<td align="left">{{tech.name}}</td>
						<td class="kindoftiny" ng-click="delete(tech.name)">Delete</td>
					</tr>
				</table></td>
			</tr>
			
			<tr>
				<td><input ng-model="newTech" size="12"/><button ng-click="add()">Add</button></td></tr>
						

		</table></td>
		
	</tr>
</table>
			
			</td></tr>
			
			
			</table>
			
</form>
</body>
</html>