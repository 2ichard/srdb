<?php

require("SignOff.php");
require("SRDb.php");

class SignOffTable {
		
	function __construct() {
	}
	
	function dumpTable() {
		$db = new SRDb();
		$signOffs = $db->getAllSignOffs();
		$buff = "<table cellpadding=\"4\" cellspacing=\"0\" border=\"1\" bordercolor=\"lightgrey\" ";
		$buff .= "align=\"center\" width=\"80%\">\n";
		$buff .= "<tr><td colspan=\"7\" align=\"center\" valign=\"center\">";
		$buff .= "<h1>Sign-Off Table</h1></td></tr>\n";
		$buff .= "<tr><th align=\"center\">Type</th><th align=\"center\">Seg</th>";
		$buff .= "<th align=\"center\">Time</th><th align=\"center\">Who</th>";
		$buff .= "<th align=\"center\">Proc</th><th align=\"center\">Sec</th>";
		$buff .= "<th align=\"center\">Step</th></tr>\n";

		for($i = 0; $i < count($signOffs); $i++) {
			$buff .= "<tr>\n";
			$buff .= sprintf("<td align=\"center\" nowrap=\"nowrap\">%s</td>", $signOffs[$i]->type);
			$buff .= sprintf("<td align=\"center\" nowrap=\"nowrap\">%s</td>", $signOffs[$i]->seg);
			$buff .= sprintf("<td align=\"center\" nowrap=\"nowrap\">%s</td>", $signOffs[$i]->t);
			$buff .= sprintf("<td align=\"center\" nowrap=\"nowrap\">%s</td>", $signOffs[$i]->who);
			$buff .= sprintf("<td align=\"center\" nowrap=\"nowrap\">%s</td>", $signOffs[$i]->proc);
			$buff .= sprintf("<td align=\"center\" nowrap=\"nowrap\">%s</td>", $signOffs[$i]->sec);
			$buff .= sprintf("<td align=\"center\" nowrap=\"nowrap\">%s</td>", $signOffs[$i]->step);
			$buff .= "</tr>\n";
		}
		$buff .= "</table>\n";
		return($buff);
	}	
	
}

if ($_GET['dump']) {
	$table = new SignOffTable();
	print $table->dumpTable();
}

?>