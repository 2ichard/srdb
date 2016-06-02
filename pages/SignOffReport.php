<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script src="../script/angular.js"></script>
<script src="../script/signOffReportController.js"></script>

<link rel="stylesheet" type="text/css" href="../css/srdb.css" />
<title>Sign-Off Report</title>
</head>

<body id="reportpagegrad" ng-app="srdbSignOffReportApp">
<form id="form1" name="form1" method="post" action="">
<table width="95%" align="center" border="1" cellspacing="0" cellpadding="5" bordercolor="#DDDDDD" 
ng-controller="SignOffReportCtrl">
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
				<td align="left" class="modeind"><span class="title1">Sign-Off Report</span></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
		</table>
		<!-- end header table -->
		
	</td></tr>
	
	<tr><td align="center">
	
	<!-- main area -->		
		
	<!-- search table -->	
	<table cellpadding="4">
	<tr>
		<td>Search:<input ng-model="query" /></td>
		<td>Matches: {{(signoffs | filter:query).length}}</td>
		<td>Sort by:
			<select name="select" ng-model="orderProp">
				<option value="seg">Segment/Lot</option>
				<option value="proc">Procedure</option>
				<option value="type">Type</option>
				</select></td></tr>
		<tr>
		<td align="right" nowrap="nowrap"><span class="modeind">Segment type:
			<select name="select2" ng-model="segType" ng-change="typeChanged()" 
			ng-options="t for t in segTypes"></select>

			S/N: </span>
				<select name="select2" ng-model="seg" ng-change="segChanged()" ng-options="s for s in sns"></select></td>
				
		<td nowrap="nowrap"><span class="modeind">Procedure:
		<select name="selectProc" ng-model="proc" ng-change="procChanged()"
		ng-options="proc for proc in procs"></select></span></td>
		
		<td nowrap="nowrap"><span class="modeind">Lot:
		<select name="selectLot" ng-model="lot" ng-change="lotChanged()"
		ng-options="lot for lot in lots"></select></span></td>
	</tr>
	</table>
	
	<br/>
	
	
	<!-- resuts table -->
	
	<table align=center cellspacing="0" cellpadding="3" border="1" bordercolor="#BBBBBB">
	<tr>
		<td align="center" ng-click="(orderProp='seg')" nowrap="nowrap">Seg or Lot</td>
		<td align="center" ng-click="(orderProp='proc')">Proc</td>
		<td ng-click="(orderProp='ver')">Ver</td>
		<td ng-click="(orderProp='sec')">Section</td>
		<td ng-click="(orderProp='step')" >Step</td>
		<td ng-click="(orderProp='type')" align="left">Type</td>
		<td align="center">Value</td>
		<td align="center">Tech</td>
		<td align="center">Timestamp</td>
		<td align="left">Notes</td>
		</tr>	
	<tr ng-repeat="signoff in signoffs | filter : query | orderBy : orderProp | orderBy: timestamp : reverse">
		<td valign="top" align="center" nowrap="nowrap">{{signoff.seg}}</td>
		<td valign="top" align="center">{{signoff.proc}}</td>
		<td valign="top" align="center">{{signoff.ver}}</td>
		<td valign="top" align="center">{{signoff.sec}}</td>
		<td valign="top" align="center">{{signoff.step}}</td>
		<td valign="top" >{{signoff.type}}</td>
		<td valign="top" align="center">{{signoff.value}}</td>
		<td valign="top" align="center">{{signoff.tech}}</td>
		<td valign="top" nowrap="nowrap">{{signoff.timestamp}}</td>
		<td valign="top" >{{signoff.notes}}</td>

	</tr> 
	</table>			
	<br />

</td></tr></table>
			
</form>
</body>
</html>