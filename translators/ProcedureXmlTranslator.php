<?php

require("../components/ProcedurePComp.php");
require("../components/Figure.php");
require("../components/ThreePartSectionSignOff.php");
require("../components/DataEntryPComp.php");
require("../components/SignOffPComp.php");
require("../lib/SignOffList.php");
require("../components/GeneralProcedurePComp.php");

class ProcedureXmlTranslator {
	
	private $srcBuff;
	private $xml;	
	private $procedure;
	private $figures = array();
	private $signoffs = array();
	private $generalProcedures = array();

	function __construct($fn) {
						
	 	// get the source file and put it in srcBuff
		$this->srcBuff = file_get_contents($fn);		
		
		// replace <html> tags with CDATA tags for html pass-through
		// this allows html to be interpretted litterally in source files
		$this->srcBuff = str_replace("<html>", "<![CDATA[", $this->srcBuff);
		$this->srcBuff = str_replace("</html>", "]]>", $this->srcBuff);
		
		// replace <p> tags (which need not be surrounded by <html> tags with CDATA
		// this is a special case to make it easy to insert paragraph breaks
		$this->srcBuff = str_replace("<p>", "<![CDATA[<p>]]>", $this->srcBuff);
	
		// translate modified XML source into XML data structure
		$this->xml = simplexml_load_string($this->srcBuff); 
		
		// translate figures
		$this->translateFigures($this->xml->procedure->figure);
		
		// translate general procedure references
		$this->translateGeneralProcedures($this->xml->procedure->general);
		
		// translate the procedure
		$this->translateProcedure();

		SignOffList::check($this->procedure->getNum(), $this->procedure->getVersion(), $this->signoffs);
		//print_r($this->xml);
	}
	
	public function getProcedure() {
		return($this->procedure);
	}
	
	private function translateProcedure() {
		$this->procedure = new ProcedurePComp(
				$this->xml->procedure['num'], 
				$this->xml->procedure['version'], 
				$this->xml->procedure['title'],
				$this->translateEquipment($this->xml->procedure->equipment),
				$this->translateIntro($this->xml->procedure->intro), 
				$this->translateSections($this->xml->procedure->section),
				$this->figures,
				$this->generalProcedures);
	}
	
	private function translateFigures($xml) {
		for ($i = 0; $i < count($xml); $i++) 
			$this->figures[(string)($xml[$i]['num'])] = new Figure(
					(string)($xml[$i]['num']), 
					(string)($xml[$i]['image']), 
					(string)($xml[$i]));
	}
	
	private function translateGeneralProcedures($xml) {
		for ($i = 0; $i < count($xml); $i++) 
			$this->generalProcedures[$i] = new GeneralProcedurePComp((string)($xml[$i]['num']));
	}
	
	private function translateEquipment($xml) {
		$items = array();
		for ($i = 0; $i < count($xml); $i++)
			$items[$i] = new EquipmentListItem($i + 1, $xml[$i]);
		return(new EquipmentList($items));
	}
	
	private function translateIntro($xml) {
		if ($xml)  {
			if ($xml['figure']) 
				$figure = $this->figures[(string)($xml['figure'])];
			else
				$figure = NULL;
			$intro = new ProcedureIntro($xml, $figure);
		}
		else
			$intro = NULL;
		return($intro);
	}
	
	private function translateSections($xml) {
		
		// array of sections
		$sections = array();
		
		// translate each section
		for ($isec = 0; $isec < count($xml); $isec++) {
			
			// translate section introduction if there is one
			if ($xml[$isec]->intro) {
				if ($xml[$isec]->intro['figure']) {
					$figure = $this->figures[(string)($xml[$isec]->intro['figure'])];
				}
				else
					$figure = NULL;
				$intro = new ProcedureSectionIntro($xml[$isec]->intro, $figure);
			}
			else
				$intro = NULL;
				
			// translate equipment list if there are equipment list items
			if ($xml[$isec]->equipment) {
				$items = array();
				for ($iequip = 0; $iequip < count($xml[$isec]->equipment); $iequip++) 
					$items[$iequip] = 
					new ProcSecEquipListItemPComp($iequip + 1, $xml[$isec]->equipment[$iequip]);
				$secEquipList = new ProcSecEquipListPComp($isec + 1, $items);
			}
			else
				$secEquipList = NULL;
				
			// translate steps
			$steps = array();
			for ($istep = 0; $istep < count($xml[$isec]->step); $istep++) {
				
				// translate substeps
				if ($xml[$isec]->step[$istep]->substep) {
					$substeps = array();
					for ($isub = 0; $isub < count($xml[$isec]->step[$istep]->substep); $isub++)
						$substeps[$isub] = new ProcedureSubstep(
							chr(ord("a") + $isub),
							$istep + 1,
							$isec + 1,
							$xml[$isec]->step[$istep]->substep[$isub]);
				}
				else
					$substeps = NULL;
					
				if ($xml[$isec]->step[$istep]['figure']) 
					$figure = $this->figures[(string)($xml[$isec]->step[$istep]['figure'])];
				else
					$figure = NULL;
				
				// data entry
				if ($xml[$isec]->step[$istep]['dataentry'])
						$dataEntry = new DataEntryPComp(
								$xml[$isec]->step[$istep]['dataentry'],
								$this->xml->procedure['num'], 
								$this->xml->procedure['version'], 
								$isec + 1, 
								$istep + 1);
				else
					$dataEntry = NULL;
				
				// step or custom sign-off
				if ($xml[$isec]->step[$istep]['signoff']) {
					if ($xml[$isec]->step[$istep]['signoff'] == "true") {
						$signoff = new SignOffPComp("step",
								$this->xml->procedure['num'], 
								$this->xml->procedure['version'], 
								$isec + 1, 
								$istep + 1);
						$this->signoffs[] = array("section" => $isec + 1, "step" => $istep + 1, "type" => "step");
					}
					else {
						$signoff = new SignOffPComp(
								$xml[$isec]->step[$istep]['signoff'],
								$this->xml->procedure['num'], 
								$this->xml->procedure['version'], 
								$isec + 1, 
								$istep + 1);
						$this->signoffs[] = array("section" => $isec + 1, "step" => $istep + 1, 
								"type" => $xml[$isec]->step[$istep]['signoff']->__toString());
					}
				}
				else
					$signoff = NULL;
					
				if ($xml[$isec]->step[$istep]['collapsable'] == "false") 
					$collapsable = false;
				else
					$collapsable = true;
				if ($xml[$isec]->step[$istep]['collapsed'] == "true") 
					$collapsed = true;
				else
					$collapsed = false;	

				$steps[$istep] = new ProcedureStep(
						$istep + 1, // step number
						$isec + 1, // section number 
						$xml[$isec]->step[$istep], // text
						$figure, $substeps, $dataEntry, $signoff, $collapsable, $collapsed); 
			}
			
			// section signoff
			if ($xml[$isec]['signoff'] == "true" || $xml[$isec]['signoff'] == "section") {
				$signoff = new SignOffPComp("section",
					$this->xml->procedure['num'], $this->xml->procedure['version'], $isec + 1);
				$this->signoffs[] = array("section" => $isec + 1, "step" => 0, "type" => "section");
			}
			else if ($xml[$isec]['signoff']) {
				$signoff = new SignOffPComp($xml[$isec]['signoff'],
					$this->xml->procedure['num'], $this->xml->procedure['version'], $isec + 1);
				$this->signoffs[] = array("section" => $isec + 1, "step" => 0, "type" => $xml[$isec]['signoff']);
			}
			else {
				$signoff = NULL;
			}
							
			// create section			
			$sections[$isec] = new ProcedureSectionPComp(
				$isec + 1, $xml[$isec]['title'], $intro, $secEquipList, $steps, $signoff);

		}
		return($sections);
	}
	
	public function getSignoffs() {
		return($this->signoffs);
	}
}
	
if ($_GET['test'] == "ProcedureXmlTranslator") {
	print("Test<br>");
	require("../lib/SRDb.php");
	$translator = new ProcedureXmlTranslator("../content.lwold/procedures/segment/SRP102_Transportation_to_HQ_Segment_Repair_Lab.txt");
	//print_r($translator->getSignoffs());
	//print($translator->getProcedure()->toHtml());
}

?>