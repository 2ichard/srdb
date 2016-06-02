<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script src="../script/util.js"></script>
<script src="../script/angular.js"></script>
<script src="../script/procDefController.js"></script>

<link rel="stylesheet" type="text/css" href="../css/srdb.css" />
<title>Procedure Definition</title>
</head>

<body id="grad6" ng-app="srdbProcDefApp">
<form id="form1" name="form1" method="post" action="">
<table width="95%" align="center" border="1" cellspacing="0" cellpadding="5" bordercolor="#DDDDDD">
	<tr>
		<td align="center">
		
		<!-- header table -->
		<table width="95%" border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td><span class="title1"><span class="title1_not_bold">
				<a href="http://www.keck.hawaii.edu/optics/segrepair/dev/db/index.html">
				Segment Repair Database</a> 
				</span></span></td>
			</tr>
			<tr>
				<td align="left" class="modeind"><span class="title1">Procedure Definition</span></td>
			</tr>
		</table>
		<!-- end header table -->
		
		</td>
	</tr>
	<tr>
		<td align="center">
			<br />
			
<div ng-controller="ProcListCtrl">
	<table border="0" cellpadding="4">
		<tr>
			<td>Search:
				<input ng-model="query" /></td>
			<td>Matches: {{(procs | filter:query).length}}</td>
			<td>Sort by:
				<select name="select" ng-model="orderProp">
					<option value="id">Procedure ID</option>
					<option value="num">Procedure Number</option>
					<option value="order">Order</option>
					<option value="title">Title</option>
					<option value="type">Type</option>

				</select></td>
			<td align="right"><button ng-click="createNew()">Create New Procedure</button></td>
		</tr>
	</table>
	
	<br/>
	
	<table align=center cellspacing="0" cellpadding="2" border="1" bordercolor="#BBBBBB">
	<tr><td><span class="kindoftiny">id</span></td>
	<td align="center" ng-click="(orderProp='type')">Type</td>
	<td align="center" ng-click="(orderProp='order')">Order</td>
	<td align="center" ng-click="(orderProp='num')">Number</td>
	<td ng-click="(orderProp='title')">Title</td>
	<td ng-click="(orderProp='filename')">Filename</td>
	<td>&nbsp;</td></tr>
	
	<tr ng-repeat="proc in procs | filter:query | orderBy:orderProp">
	
		<!-- id -->
		<td ng-hide="inedit[proc.id]" align="right" valign="top" ng-click="edit(proc.id)">
			<span class="kindoftiny">{{proc.id}}</span></td>
		<td ng-show="inedit[proc.id]" align="right" valign="top">{{proc.id}}</td>
			
		<!--  type -->
		<td ng-hide="inedit[proc.id]" align="center" valign="top" ng-click="edit(proc.id)">
			{{proc.type}}</td>
		<td ng-show="inedit[proc.id]" align="center" valign="top">
			<select ng-model="proc.type" ng-options="t for t in ['General', 'Segment', 'Batch']"/></td>

		<!--  order -->
		<td ng-hide="inedit[proc.id]" align="center" valign="top" ng-click="edit(proc.id)">
			{{proc.order}}</td>
		<td ng-show="inedit[proc.id]" align="right" valign="top">
			<input size="5" ng-model="proc.order" /></td>
			
		<!-- number -->
		<td ng-hide="inedit[proc.id]" align="right" valign="top" ng-click="edit(proc.id)">
			{{proc.num}}</td>
		<td ng-show="inedit[proc.id]" align="right" valign="top">
			<input size="8" ng-maxlength="8" ng-model="proc.num" /></td>
			
		<!-- title -->
		<td ng-hide="inedit[proc.id]" align="left" valign="top" ng-click="edit(proc.id)">
			{{proc.title}}</td>
		<td ng-show="inedit[proc.id]" align="left" valign="top">
			<input size="30" ng-maxlength="64" ng-model="proc.title" /></td>
			
		<!-- filename -->
		<td ng-hide="inedit[proc.id]" align="left" valign="top" ng-click="edit(proc.id)">
			{{proc.filename}}</td>
		<td ng-show="inedit[proc.id]" align="left" valign="top">
			<input size="30" ng-maxlength="64" ng-model="proc.filename" /></td>
			
		<!-- controls -->
		<td align="left" valign="top" nowrap="nowrap" ng-hide="inedit[proc.id]">
			<button ng-click="edit(proc.id)">Edit</button>
			<button ng-click="openProcedure(proc.type, proc.num)">Open</button></td>
		<td ng-show="inedit[proc.id]" align="left" valign="top" nowrap>
			<button ng-click="save(proc.id)">Save</button>
			<button ng-click="unedit(proc.id)">Cancel</button>
			<button ng-click="delete(proc.id)">Delete</button></td>
	<tr>
	</table>
	

	<br />
	<br />
			</div>
			
			
			
			</td></tr>
			
			</table>
			
</form>
</body>
</html>