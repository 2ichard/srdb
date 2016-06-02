<?php

require("Procedure.php");
require("SRDb.php");

class ProcedureTable {
	
	private $db;
	
	function __construct() {
		
		$this->db = new SRDb();
	}
	
	function dumpTable() {
		
		$procedures = $this->db->getAllProcs();
		$buff = "<table cellpadding=\"2\" cellspacing=\"0\" border=\"1\" bordercolor=\"lightgrey\" ";
		$buff .= "align=\"center\" width=\"80%\">\n";
		$buff .= "<tr><td colspan=\"4\" align=\"center\" valign=\"center\"><h1>Procedures Table</h1></td></tr>\n";
		$buff .= "<tr><th align=\"center\">Num</th><th align=\"center\">Type</th>";
		$buff .= "<th align=\"center\">Seq</th><th align=\"left\">Title</th></tr>\n";
		for($i = 0; $i < count($procedures); $i++) {
			$buff .= "<tr>\n";
			$buff .= sprintf("<td align=\"center\">%s</td>", $procedures[$i]->getNum());
			$buff .= sprintf("<td align=\"center\">%s</td>", $procedures[$i]->getType());
			$buff .= sprintf("<td align=\"center\">%s</td>", $procedures[$i]->getSeq());
			$buff .= sprintf("<td align=\"left\" nowrap=\"nowrap\">%s</td>", $procedures[$i]->getTitle());
			$buff .= "</tr>\n";
		}
		$buff .= "</table>\n";
		return($buff);
	}	
}

if ($_GET['dump']) {
	$table = new ProcedureTable();
	print $table->dumpTable();
}

?>