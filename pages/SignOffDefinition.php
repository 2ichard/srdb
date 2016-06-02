<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script src="../script/angular.js"></script>
<script src="../script/signOffDefController.js"></script>

<link rel="stylesheet" type="text/css" href="../css/srdb.css" />
<title>Sign-Off Definition</title>
</head>

<body id="grad6" ng-app="srdbSignOffDefApp">
<form id="form1" name="form1" method="post" action="">
<table width="95%" align="center" border="1" cellspacing="0" cellpadding="5" bordercolor="#DDDDDD" ng-controller="SignOffListCtrl">
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
				<td align="left" class="modeind"><span class="title1">Sign-Off Definition</span></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
		</table>
		<!-- end header table -->
		
		</td>
	</tr>
	<tr>
		<td align="center">
			
			
			<table cellpadding="4">
		<tr>
			<td>Search:
				<input ng-model="query" /></td>
			<td>Matches: {{(desc | filter:query).length}}</td>
			<td>Sort by:
				<select name="select" ng-model="orderProp">
					<option value="id">ID</option>
					<option value="type">Type</option>
					<option value="title">Title</option>
					<option value="valType">Value Type</option>

				</select></td>
				<td align="right"><button ng-click="createNew()">Create New Sign-Off</button></td>
		</tr>
	</table>
	
	<br/>
	
			<table align=center cellspacing="0" cellpadding="3" border="1" bordercolor="#BBBBBB">
			<tr>
				<td ng-click="(orderProp='id')"><span class="kindoftiny">ID</span></td>
				<td ng-click="(orderProp='type')">Type</td>
				<td ng-click="(orderProp='title')">Title</td><td>Label</td>
				<td align="center" ng-click="(orderProp='valType')" nowrap="nowrap">Value Type</td>
				<td align="center">Min</td>
				<td align="center">Max</td>
				<td>Choices</td><td>&nbsp;</td></tr>
		
			<tr ng-repeat="d in desc | filter:query | orderBy:orderProp">
			
			<!-- id -->
			<td ng-hide="inedit[d.id]" valign="top" ng-click="edit(d.id)">
				<span class="kindoftiny">{{d.id}}</span></td>
			<td ng-show="inedit[d.id]" align="right" valign="top">{{d.id}}</td>
			
			<!-- type -->
			<td ng-hide="inedit[d.id]" valign="top" ng-click="edit(d.id)">{{d.type}}</td>
			<td ng-show="inedit[d.id]" align="right" valign="top">
				<input size="20" ng-maxLength="16" ng-model="d.type"></input></td>
				
			<!-- title -->
			<td ng-hide="inedit[d.id]" valign="top" ng-click="edit(d.id)">{{d.title}}</td>
			<td ng-show="inedit[d.id]" align="left" valign="top">
				<input size="20" ng-maxLength="64" ng-model="d.title"></input></td>
				
			<!-- label -->
			<td ng-hide="inedit[d.id]" valign="top" ng-click="edit(d.id)">{{d.label}}</td>
			<td ng-show="inedit[d.id]" align="left" valign="top">
				<input size="8" ng-maxLength="8" ng-model="d.label"></input></td>
				
			<!-- value type -->
			<td ng-hide="inedit[d.id]" align="center" valign="top" ng-click="edit(d.id)">
				{{d.valType}}</td>
			<td ng-show="inedit[d.id]" align="center" valign="top">
				<select ng-model="d.valType" ng-options="t for t in ['Choice', 'String', 'Integer', 'Float']"/></td>
				
			<!-- min -->
			<td ng-hide="inedit[d.id]" align="right" valign="top" ng-click="edit(d.id)">
					{{d.minVal}}</td>
			<td ng-show="inedit[d.id]" align="right" valign="top">
				<input size="8" ng-maxLength="8" ng-model="d.minVal"></td>
				
			<!-- max -->
			<td ng-hide="inedit[d.id]" align="right" valign="top" ng-click="edit(d.id)">
					{{d.maxVal}}</td>
			<td ng-show="inedit[d.id]" align="right" valign="top">
				<input size="8" ng-maxLength="8" ng-model="d.maxVal"></td>

			<!-- choices -->
			<td ng-hide="inedit[d.id]" valign="top" ng-click="edit(d.id)">
				<span ng-repeat="c in d.choices">{{c}}, </span></td>
			<td ng-show="inedit[d.id]" align="left" valign="top">
				<textarea rows="1" cols="30" ng-model="d.choices"></textarea></td>
				
			<!-- controls -->
			<td ng-hide="inedit[d.id]" align="left" valign="top">
				<button ng-click="edit(d.id)">Edit</button></td>
			<td ng-show="inedit[d.id]" align="left" valign="top" nowrap>
				<button ng-click="save(d.id)">Save</button>
				<button ng-click="unedit(d.id)">Cancel</button>
				<button ng-click="delete(d.id)">Delete</button></td>
			
			</tr>
			
			<!--
			<tr>
				<td colspan="9">inedit={{inedit}}</td></tr>
			-->
			
			
			</table>			<br />
			
			</td></tr>
			
			</table>
			
</form>
</body>
</html>