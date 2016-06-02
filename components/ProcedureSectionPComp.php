<?php

require("ProcedureSectionIntro.php");
require("ProcSecEquipListPComp.php");
require("ProcedureStep.php");

class ProcedureSectionPComp {

	public $num;
	public $title;
	public $intro;
	public $equipList;
	public $steps;
	public $signoff;
	
	private $anchorName;
	
	function __construct($num, $title, $intro, $equipList, $steps, $signoff) {
		$this->num = $num;
		$this->title = $title;
		$this->intro = $intro;
		$this->equipList = $equipList;
		$this->figure = $figure;
		$this->steps = $steps;
		$this->signoff = $signoff;
		$this->anchorName = "section_" . $num;
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/procedure_section_template.html");
		$buff = str_replace("%%num%%", $this->num, $buff);
		$buff = str_replace("%%title%%", $this->title, $buff);
		$buff = str_replace("%%anchor_name%%", $this->anchorName, $buff);

		// insert introduction, if there is one
		
		if ($this->intro == NULL)
			$buff = str_replace("%%intro%%", "", $buff);
		else
			$buff = str_replace("%%intro%%", $this->intro->toHtml(), $buff);

		// insert equipment list, if there is one
		if ($this->equipList == NULL)
			$buff = str_replace("%%equipment_list%%", "", $buff);
		else
			$buff = str_replace("%%equipment_list%%", $this->equipList->toHtml(), $buff);

		// insert steps
		
		if ($this->steps == NULL)
			$buff = str_replace("%%steps%%", "", $buff);
		else {
			$stepBuff = "";
			for ($istep = 0; $istep < count($this->steps); $istep++)
				$stepBuff .= $this->steps[$istep]->toHtml();
			$buff = str_replace("%%steps%%", $stepBuff, $buff);
		}
		
		
		// insert section sign-off, if there is one
		if ($this->signoff)
			$buff = str_replace("%%signoff%%", $this->signoff->toHtml(), $buff);
		else
			$buff = str_replace("%%signoff%%", "", $buff);

		return($buff);
	}	
	
	public function getAnchorName() {
		return($this->anchorName);
	}
	
	public function getTitle() {
		return($this->title);
	}
	
	public function getNum() {
		return($this->num);
	}
}

if ($_GET['test'] == "ProcedureSection") {
	
	require("../pages/Page.php");
	
	class ProcedureSectionTestPage extends Page {
		
		function __construct() {
			parent::__construct("../templates/page_template.html");
		}
		
		protected function gen() {
			
			Page::replaceTag($this->pageBuff, "type", "");
			Page::replaceTag($this->pageBuff, "title", "Procedure Section Component Test");
			
			
			$section = new ProcedureSection("123", "Foo Title", NULL, "", NULL);
			$content = $section->toHtml();
	
			$intro = new ProcedureSectionIntro("This is the intro text", NULL);
			$section = new ProcedureSection("123", "Foo Title", $intro, "", NULL);
			$content .= $section->toHtml();
	
			require("StepSignOffComp.php");

			$steps = array();
			$steps[] = new ProcedureStep("1", "2", "Step 1 text.", NULL, NULL, new StepSignOffComp("SRP0000", 123, 1));
			$steps[] = new ProcedureStep("2", "2", "Step 2 text.", NULL, NULL, new StepSignOffComp("SRP0000", 123, 2));
			$section = new ProcedureSection("123", "Foo Title", $intro, $steps, NULL);
			$content .= $section->toHtml();
			
			Page::replaceTag($this->pageBuff, "content", $content);
		}
	}
	new ProcedureSectionTestPage();
}
?>


