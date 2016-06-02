<?php

//require("../lib/SRDb.php");

class SignOffSummaryPComp {

	
	function __construct() {

	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/signoff_summary_template.html");
		$buff = str_replace("%%type%%", $this->type, $buff);

		return($buff);
	}	
}


?>