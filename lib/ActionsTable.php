<?php

//require("../lib/SRDb.php");
require("../lib/Action.php");

class ActionsTable {

	private $step_id;
	private $db;
	private $actions = array();
	
	
	function __construct($step_id) {
		$this->step_id = $sid;
		$this->db = new SRDb();
		
		$this->actions = $this->db->getActionsByStep($step_id);		
		
		
	}
	
	function getTable() {
		$buff = "";
		$buff .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"1\" width=\"90%\" align=\"center\">\n";
		$buff .= "<tr><td>Seg</td><td>Tech</td><td>Station</td><td>Time</td><td>Notes</td></tr>\n";
		for ($i = 0; $i < count($this->actions); $i++) {
			$buff .= "<tr>";
			$buff .= sprintf("<td>%s</td>", $this->actions[$i]->seg);
			$buff .= sprintf("<td>%s</td>", $this->actions[$i]->tech);
			$buff .= sprintf("<td>%s</td>", $this->actions[$i]->station);
			$buff .= sprintf("<td>%s</td>", $this->actions[$i]->time);
			$buff .= sprintf("<td>%s</td>", $this->actions[$i]->notes);
			$buff .= "</tr>\n";
		}
		$buff .= "</table>";
		return($buff);
	}
}
	
if ($_GET['step_id']) {
	try {
		$table = new ActionsTable($_GET['step_id']);
		print $table->getTable();
	}
	catch(Exception $e) {
		exit($e->getMessage());
	}
}


?>