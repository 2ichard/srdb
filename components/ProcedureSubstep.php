<?php

class ProcedureSubstep {
	
	private $substepNum;
	private $stepNum;
	private $sectionNum;
	private $text;
	
	function __construct($substepNum, $stepNum, $sectionNum, $text) {
		$this->substepNum = $substepNum;
		$this->stepNum = $stepNum;
		$this->sectionNum = $sectionNum;
		$this->text = $text;
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/procedure_substep_template.html");
		$buff = str_replace("%%num%%", $this->sectionNum . "." . $this->stepNum . "." . $this->substepNum . ") ", $buff);
		$buff = str_replace("%%text%%", $this->text, $buff);
		return($buff);
	}
}

if ($_GET['test'] == "ProcedureSubstep") {
	$substep = new ProcedureSubstep("a", "1", "2", "This is the substep text.");
	print($substep->toHtml());
}
?>