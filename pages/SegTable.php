<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script src="../script/angular.js"></script>
<script src="../script/segTableController.js"></script>

<link rel="stylesheet" type="text/css" href="../css/srdb.css" /><style type="text/css">
</style>
<title>Segment Table</title>
</head>

<body id="grad6" ng-app="srdbSegTableApp">
<form id="form1" name="form1" method="post" action="">
<table width="95%" align="center" border="1" cellspacing="0" cellpadding="5" bordercolor="#DDDDDD" ng-controller="SegTableCtrl">
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
				<td align="left" class="modeind"><span class="title1">Segment Table</span></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
		</table>
		<!-- end header table -->
		
		</td>
	</tr>
	
	<tr >
		<td align="center">
			
			<table border="1" cellspacing="0" cellpadding="2" bordercolor="#CCCCCC">
	<tr >
		<td ng-repeat="type in types"><table border="0" cellspacing="2" cellpadding="2" bordercolor="#CCCCCC">
			<tr>
				<td align="center" nowrap="nowrap">Type {{type}}</td>
			</tr>
			<tr>
				<td align="center">
				<table border="1" bordercolor="#CCCCCC" align="center" cellpadding="2" cellspacing="0">
					<tr>
						<td class="title2"><span class="kindoftiny">ID</span></td>
						<td align="center">S/N</td>
						<td>&nbsp;</td>
					</tr>
					<tr ng-repeat="s in segs[type] | orderBy : 'sn'">
						<td align="right" class="title2"><span class="kindoftiny">{{s.id}}</span></td>
						<td align="center">{{s.sn}}</td>
						<!--<td><button ng-click="delete(s.id)">Delete</button></td>-->
						<td class="kindoftiny" ng-click="edit(s, type)">Edit</td>
					</tr>
				</table></td>
			</tr>
		</table></td>
		
	</tr>
</table>
			
			</td></tr>
			
			<tr>
		<td align="center"><table border="1" cellspacing="0" cellpadding="4" bordercolor="#CCCCCC">
			<tr>
				<td align="center" class="title2"><span class="kindoftiny">ID</span></td>
				<td>Type</td>
				<td>S/N</td>
				<td>&nbsp;</td>
				</tr>
			<tr>
				<td align="center" class="kindoftiny">{{inedit.id}}</td>
				<td><select ng-model="inedit.type" ng-options="t for t in types"></select></td>
				<td><input ng-model="inedit.sn" size="5"/></td>
				<td>
					<button ng-click="save()">Save</button>
					<button ng-click="clear()">Clear</button>
					<button ng-click="delete(inedit.id)">Delete</button></td>
				</tr>
		</table>
</td></tr>
			
			</table>
			
</form>
</body>
</html>