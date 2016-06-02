<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script src="../script/angular.js"></script>
<script src="../script/procNotesReportController.js"></script>

<link rel="stylesheet" type="text/css" href="../css/srdb.css" />
<title>Procedure Notes Report</title>
</head>

<body id="reportpagegrad" ng-app="srdbProcNotesReportApp">
<form id="form1" name="form1" method="post" action="">
<table width="95%" align="center" border="1" cellspacing="0" cellpadding="5" bordercolor="#DDDDDD" 
ng-controller="ProcNotesReportCtrl">
	<tr><td align="center">
				
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
				<td align="left" class="modeind"><span class="title1">Procedure Notes Report</span></td>
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
		<td>Matches: {{(notes | filter:query).length}}</td>
		<td>Sort by:
			<select name="select" ng-model="orderProp">
				<option value="seg">Segment</option>
				<option value="proc">Procedure</option>
				<option value="tech">Tech</option>
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
		<td align="center" ng-click="(orderProp='ver')">Ver</td>
		<td align="center" ng-click="(orderProp='tech')">Tech</td>
		<td align="center" ng-click="(orderProp='timestamp')" align="center">Timestamp</td>
		<td>Note</td>
		</tr>	
	<tr ng-repeat="note in notes | filter : query | orderBy:orderProp | orderBy: timestamp : reverse">
		<td align="center" valign="top" nowrap="nowrap">{{note.seg}}</td>
		<td align="center" valign="top">{{note.proc}}</td>
		<td align="center" valign="top">{{note.ver}}</td>
		<td align="center" valign="top">{{note.tech}}</td>
		<td align="center" valign="top" nowrap="nowrap">{{note.timestamp}}</td>
		<td align="left" valign="top"><div ng-bind-html="note.note | unsafe"></div></td>

	</tr> 
	</table>			
	<br />
</td></tr></table>
			
</form>
</body>
</html>