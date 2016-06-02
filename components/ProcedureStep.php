<?php

require("ProcedureSubstep.php");

class ProcedureStep {

	private $stepNum;
	private $secNum;
	private $text;
	private $figure;
	private $substeps;
	private $dataEntry;
	private $signoff;
	private $collapsable;
	private $initiallyCollapsed;
	
	function __construct($stepNum, $secNum, $text, $figure, $substeps, $dataEntry, $signoff, 
			$collapsable = true, $initiallyCollapsed = false) {
		$this->stepNum = $stepNum;
		$this->secNum = $secNum;
		$this->text = $text;
		$this->figure = $figure;
		$this->substeps = $substeps;
		$this->dataEntry = $dataEntry;
		$this->signoff = $signoff;
		$this->collapsable = $collapsable;
		$this->initiallyCollapsed = $initiallyCollapsed;
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/procedure_step_template.html");
		$buff = str_replace("%%sec_num%%", $this->secNum, $buff);
		$buff = str_replace("%%step_num%%", $this->stepNum, $buff);

		// substeps
		if ($this->substeps == NULL) {
			// no substeps for this step
			$buff = str_replace("%%substeps%%", "", $buff);
			$buff = str_replace("%%collapsable%%", "false", $buff);
			$buff = str_replace("%%initially_collapsed%%", "true", $buff);
		}
		else {
			// assemble substeps in $substepsBuff
			$substepsBuff = "";
			for ($isub = 0; $isub < count($this->substeps); $isub++) 
				$substepsBuff .= $this->substeps[$isub]->toHtml();
			
			$buff = str_replace("%%substeps%%", $substepsBuff, $buff);
			if ($this->collapsable)
				$buff = str_replace("%%collapsable%%", "true", $buff);
			else
				$buff = str_replace("%%collapsable%%", "false", $buff);

			if ($this->initiallyCollapsed)
				$buff = str_replace("%%initially_collapsed%%", "true", $buff);
			else
				$buff = str_replace("%%initially_collapsed%%", "false", $buff);
		}
		
		// figure
		if ($this->figure) {
			$buff = str_replace("%%colspan%%", "1", $buff);
			$buff = str_replace("%%figure%%", $this->figure->getThumbnailHtml(), $buff);
			$buff = str_replace("%%show_figure%%", "true", $buff);
		}
		else {
			$buff = str_replace("%%colspan%%", "2", $buff);
			$buff = str_replace("%%figure%%", "", $buff);
			$buff = str_replace("%%show_figure%%", "false", $buff);
		}

		$buff = str_replace("%%text%%", $this->text, $buff);
		
		// data entry
		if ($this->dataEntry) 
			$buff = str_replace("%%data_entry%%", $this->dataEntry->toHtml(), $buff);
		else 
			$buff = str_replace("%%data_entry%%", "", $buff);
		
		// sign-off
		if ($this->signoff) 
			$buff = str_replace("%%signoff%%", $this->signoff->toHtml(), $buff);
		else 
			$buff = str_replace("%%signoff%%", "", $buff);

		
		return($buff);
	}	
}

if ($_GET['test'] == "ProcedureStep") {
	require("../pages/Page.php");
	require("StepSignOffComp.php");
	
	class ProcedureStepTestPage extends Page {
		function __construct() {
			parent::__construct("../templates/page_template.html");
		}
		
		protected function gen() {
			
			Page::replaceTag($this->pageBuff, "type", "");
			Page::replaceTag($this->pageBuff, "title", "Procedure Step Component Test");
			
			$step1 = new ProcedureStep("5", "123", "Foo text", "", NULL, 
				new StepSignOffComp("SRP0000", 5, 123));
			$content = $step1->toHtml() . "<br>";
	
			$substeps = array();
			$substeps[] = new ProcedureSubstep("a", "1", "1", "Substep a text.");
			$substeps[] = new ProcedureSubstep("b", "1", "1", "Substep b text.");
			$step2 = new ProcedureStep("5", "123", "Foo text", "", $substeps, NULL);
			$content .= $step2->toHtml();
			
			Page::replaceTag($this->pageBuff, "content", $content);
		}
	}
	
	new ProcedureStepTestPage();
}
?>