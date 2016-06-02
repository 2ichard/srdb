<?php
require("ProcedureTOC.php");
require("SignOffSummaryPComp.php");
require("ProcedureIntro.php");
require("ProcedureSectionPComp.php");
require("EquipmentList.php");
require("GeneralProceduresList.php");

class ProcedurePComp {
	
	private $num;
	private $title;
	private $version;
	private $equipment;
	private $intro;
	private $sections;
	private $figures;
	private $generalProcedures;
	
	public function __construct($num, $version, $title, $equipment, $intro, $sections, $figures, 
			$generalProcedures) {
		$this->num = $num;
		$this->title = $title;
		$this->version = $version;
		$this->equipment = $equipment;
		$this->intro = $intro;
		$this->sections = $sections;
		$this->figures = $figures;
		$this->generalProcedures = $generalProcedures;
	}
	
	public function getNum() {
		return($this->num);
	}
	
	public function getTitle() {
		return($this->title);
	}
	
	public function getVersion() {
		return($this->version);
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/procedure_template.html");

		// insert table of contents
		$toc = new ProcedureTOC($this->intro, $this->equipment, $this->sections);
		$buff = str_replace("%%table_of_contents%%", $toc->toHtml(), $buff);
		
		// insert sign-off summary
		$signOffSummary = new SignOffSummaryPComp();
		$buff = str_replace("%%sign_off_summary%%", $signOffSummary->toHtml(), $buff);
			
		// insert procedure introduction
		if ($this->intro == NULL)
			$buff = str_replace("%%intro%%", "", $buff);
		else
			$buff = str_replace("%%intro%%", $this->intro->toHtml(), $buff);
			
		// insert procedure sections
		$secBuff = "";
		for ($isec = 0; $isec < count($this->sections); $isec++)
			$secBuff .= $this->sections[$isec]->toHtml();
		$buff = str_replace("%%sections%%", $secBuff, $buff);
		
		// replace any references to general procedures with links
		for ($i = 0; $i < count($this->generalProcedures); $i++) {
			$buff = str_replace($this->generalProcedures[$i]->getNum(), 
				$this->generalProcedures[$i]->getShortLinkHtml(), $buff);
		}

		// insert general procedures list
		$generalProceduresList = new GeneralProceduresList($this->generalProcedures);
		$buff = str_replace("%%general_procedures%%", $generalProceduresList->toHtml(), $buff);

		// replace figure tags
		if (count($this->figures) > 9)
			for ($i = 10; $i <= count($this->figures); $i++)
				$buff = str_replace("Figure " . $i, "Figure x" . $i, $buff);

		for ($i = 1; $i <= count($this->figures) && $i < 10; $i++) 
			$buff = str_replace("Figure " . $i, $this->figures[$i]->getLinkHtml(), $buff);
		
		if (count($this->figures) > 9)
			for ($i = 10; $i <= count($this->figures); $i++)
				$buff = str_replace("Figure x" . $i, $this->figures[$i]->getLinkHtml(), $buff);

		return($buff);
	}
}
?>