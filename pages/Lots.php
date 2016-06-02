<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script src="../script/angular.js"></script>
<script src="../script/lotsController.js"></script>

<link rel="stylesheet" type="text/css" href="../css/srdb.css" />
<title>Lots & Batches</title>
</head>

<body id="grad6" ng-app="srdbLotsApp">
<form id="form1" name="form1" method="post" action="">
<table width="95%" align="center" border="1" cellspacing="0" cellpadding="5" bordercolor="#DDDDDD" 
		ng-controller="LotsCtrl">
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
				<td align="left" class="modeind"><span class="title1">Lots &amp; Batches</span></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
		</table>
		<!-- end header table -->
		
		</td>
	</tr>
	<tr>
		<td align="center">
		
				<br />
		<table width="65%" border="0" cellspacing="0" cellpadding="8" bgcolor="#EEEEEE">
			<tr>
				<td><p>Notes: </p>
					<ul>
						<li>Only <strong>Active</strong> lots/batches appear in procedure data entry drop-down menus.<br />
							<br />
						</li>
						<li>Lots/batches are automatically made Active upon creation.<br />
							<br />
						</li>
						<li>To change the status of a lot/batch, press the <span class="table_cell">Edit</span> button for that lot/batch and choose <span class="table_cell">Active</span> or <span class="table_cell">Inactive</span> from the drop-down menu in the <span class="table_cell">Status</span> column, and then press <span class="table_cell">Save</span>.</li>
					</ul></td>
			</tr>
		</table>
		<br />
		
		<table cellpadding="4">
		<tr>
			<td>Search:
				<input ng-model="query" /></td>
			<td>Matches: {{(desc | filter:query).length}}</td>
			<td>Sort by:
				<select name="select" ng-model="orderProp">
					<option value="id">ID</option>
					<option value="lot_num">Lot Number</option>
					<option value="type">Type</option>
					<option value="q_init">Quantity, initial</option>
					<option value="q_now">Quantity, now</option>
					<option value="status">Status</option>
					<!--<option value="timestamp">Timestamp</option>-->
				</select></td>
				<td align="right"><button ng-click="createNew()">Create New Lot</button></td>
		</tr>
	</table>
	
	<br/>
	<table align=center cellspacing="0" cellpadding="3" border="1" bordercolor="#BBBBBB">
			<tr>
				<td valign="bottom" ng-click="(orderProp='id')">ID</td>
				<td valign="bottom" ng-click="(orderProp='lot_num')">Lot Number</td>
				<td valign="bottom" ng-click="(orderProp='type')">Type</td>
				<td align="right" valign="bottom" ng-click="(orderProp ='q_init')">Quantity,<br />initial</td>
				<td align="right" valign="bottom" ng-click="(orderProp='q_now')" nowrap="nowrap">Quantity,<br />now</td>
				<td valign="bottom" ng-click="(orderProp='status')" align="center">Status</td>
				<!--<td valign="bottom" ng-click="(orderProp='timestamp')" align="center">Last Change</td>--></tr>
		
			<tr ng-repeat="lot in lots | filter:query | orderBy:orderProp">
			
			<!-- id -->
			<td ng-hide="inedit[lot.id]" valign="top" ng-click="edit(lot.id)">
				<span class="kindoftiny">{{lot.id}}</span></td>
			<td ng-show="inedit[lot.id]" align="right" valign="top">{{lot.id}}</td>
			
			<!-- lot number -->
			<td ng-hide="inedit[lot.id] && lot.id == 0" valign="top" ng-click="edit(lot.id)">{{lot.lot}}</td>
			<td ng-show="inedit[lot.id] && lot.id == 0" align="left" valign="top" nowrap>
				<input size="12" ng-maxLength="16" ng-model="lot.lot"></input>
				<button ng-show="(lot.id == 0)" ng-click='genLotNo()'>Auto</button></td>
				
			<!-- type -->
			<td ng-hide="inedit[lot.id] && lot.id == 0" valign="top" ng-click="edit(lot.id)">{{lot.type}}</td>
			<td ng-show="inedit[lot.id] && lot.id == 0" align="left" valign="top">
				<select ng-model="lot.type" ng-options="t for t in lotTypes"/></td>
				
			<!-- quantity, initial -->
			<td ng-hide="inedit[lot.id] && lot.id != 0" align="right" valign="top" ng-click="edit(lot.id)">
				{{lot.q_init}}</td>
			<td ng-show="inedit[lot.id] && lot.id != 0" align="left" valign="top">
				<input size="6" ng-maxLength="16" ng-model="lot.q_init"></input></td>
				
			<!-- quantity, now -->
			<td ng-hide="inedit[lot.id] && lot.id != 0" align="right" valign="top" ng-click="edit(lot.id)">
				{{lot.q_now}}</td>
			<td ng-show="inedit[lot.id] && lot.id != 0" align="left" valign="top">
				<input size="6" ng-maxLength="8" ng-model="lot.q_now"></input></td>
				
			<!-- status -->
			<td ng-hide="inedit[lot.id] && lot.id != 0" align="center" valign="top" ng-click="edit(lot.id)">
				{{lot.status}}</td>
			<td ng-show="inedit[lot.id] && lot.id != 0" align="left" valign="top">
				<select ng-model="lot.status" ng-options="t for t in ['Active', 'Inactive']"/></td>
				
			<!-- last change -->
			<!--
			<td ng-hide="inedit[lot.id]" valign="top" ng-click="edit(lot.id)">{{lot.timestamp}}</td>
			<td ng-show="inedit[lot.id]" align="left" valign="top">&nbsp;</td>
			-->
				
			<!-- controls -->
			<td ng-hide="inedit[lot.id]" align="left" valign="top">
				<button ng-click="edit(lot.id)">Edit</button></td>
			<td ng-show="inedit[lot.id]" align="left" valign="top" nowrap>
				<button ng-click="save(lot.id)">Save</button>
				<button ng-click="unedit(lot.id)">Cancel</button>
				<button ng-show="(lot.id != 0)" ng-click="delete(lot.id)">Delete</button></td>
			
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