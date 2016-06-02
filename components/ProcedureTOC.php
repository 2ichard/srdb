<?php

require("ProcedureTOCItem.php");

class ProcedureTOC {
	
	private $intro;
	private $equipment;
	private $sections;
	
	function __construct($intro, $equipment, $sections) {
		$this->intro = $intro;
		$this->equipment = $equipment;
		$this->sections = $sections;
	}
	
	public function toHtml() {
			
		$buff = file_get_contents("../templates/procedure_toc_template.html");
		
		$itemBuff = "";
		
		if ($this->intro != NULL) {
			$item = new ProcedureTOCItem("Introduction", $this->intro->getAnchorName());
			$itemBuff .= $item->toHtml();
		}
		
		for ($i = 0; $i < count($this->sections); $i++) {
			$item = new ProcedureTOCItem("Section " . $this->sections[$i]->getNum()	. " - " . 
				$this->sections[$i]->getTitle(), $this->sections[$i]->getAnchorName());
			$itemBuff .= $item->toHtml();
		}
		
		$buff = str_replace("%%left_items%%", $itemBuff, $buff);
		
		$itemBuff = "";
			
		$item = new ProcedureTOCItem("General Procedures", "general_procedures_list");
		$itemBuff .= $item->toHtml();
		
		$item = new ProcedureTOCItem("Sign-Off Summary", "signoff_summary");
		$itemBuff .= $item->toHtml();
			
		$item = new ProcedureTOCItem("Non-Conformance Records (0)", "");
		$itemBuff .= $item->toHtml();
		
		$item = new ProcedureTOCItem("Notes", "notes");
		$itemBuff .= $item->toHtml();
		
		$buff = str_replace("%%right_items%%", $itemBuff, $buff);

		return($buff);
	}	
}

if ($_GET['test'] == "ProcedureTOC") {
	
	require("../pages/Page.php");
	require("ProcedureIntro.php");
	
	class ProcedureTOCTestPage extends Page {
				
		function __construct() {
			parent::__construct("../templates/page_template.html");
		}
		
		protected function gen() {
			
			Page::replaceTag($this->pageBuff, "type", "");
			Page::replaceTag($this->pageBuff, "title", "Procedure TOC Component Test");
		
			$intro = new ProcedureIntro("Intro text.", NULL);
			$toc = new ProcedureTOC($intro, NULL, NULL);

			Page::replaceTag($this->pageBuff, "content", $toc->toHtml());
		}
	}
	new ProcedureTOCTestPage();
}
?>


