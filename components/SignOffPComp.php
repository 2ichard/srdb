<?php

//require("../lib/SRDb.php");

class SignOffPComp {

	private $type;
	private $proc;
	private $version;
	private $sec;
	private $step;
	private $signOffDesc;
	
	function __construct($type, $proc, $version, $sec, $step = 0) {
		$this->type = $type;
		$this->proc = $proc;
		$this->version = $version;
		$this->sec = $sec;
		$this->step = $step;
		
		// get sign-off description from database
		$this->srdb = new SRDb();
		$this->signOffDesc = $this->srdb->getSignoffDesc($type);
	}
	
	public function toHtml() {
		
		// get sign-off HTML template
		$buff = file_get_contents("../templates/signoff_template.html");
		
		// replace template placeholders with values
		$buff = str_replace("%%type%%", $this->type, $buff);
		$buff = str_replace("%%title%%", $this->signOffDesc[0]['title'], $buff);
		$buff = str_replace("%%label%%", $this->signOffDesc[0]['label'], $buff);
		$buff = str_replace("%%valType%%", $this->signOffDesc[0]['valType'], $buff);
		$buff = str_replace("%%minVal%%", $this->signOffDesc[0]['minVal'], $buff);
		$buff = str_replace("%%maxVal%%", $this->signOffDesc[0]['maxVal'], $buff);
		$buff = str_replace("%%proc%%", $this->proc, $buff);
		$buff = str_replace("%%ver%%", $this->version, $buff);
		$buff = str_replace("%%sec%%", $this->sec, $buff);
		$buff = str_replace("%%step%%", $this->step, $buff);	
		$buff = str_replace("%%id%%", "sec" . $this->sec . "Step" . $this->step, $buff);
		
		// if this is a "choice" sign-off then construct choice menu
		if ($this->signOffDesc[0]['valType'] == "Choice") {
			$buff = str_replace("%%has_menu%%", "true", $buff);
			$choices = "";
			
			// if this is a batch consume signoff then get batch numbers from database
			if (strpos($this->signOffDesc[0]['choices'][0], "batch=") !== false) {
				
				try {
					$batchType = substr($this->signOffDesc[0]['choices'][0], 
							strpos($this->signOffDesc[0]['choices'][0], "=") + 1);
							

					$lotsResp = $this->srdb->getLots($batchType, true);
					for ($i = 0; $i < count($lotsResp); $i++) {
						if ($i > 0)
							$choices .+ ", ";
						$choices .= "'" . $lotsResp[$i]['lot'] .= "'";
					}
					
	//				print($choices);
//					exit();
					
				}
				catch(Exception $e) {
					$choices = "unknown lot numbers";
				}
			}
			
			else {
				for ($i = 0; $i < count($this->signOffDesc[0]['choices']); $i++) {
					if ($i > 0)
						$choices .= ", ";
					$choices .= "'" . $this->signOffDesc[0]['choices'][$i] . "'";
				}
			}
			$buff = str_replace("%%choices%%", $choices, $buff);
		}
		else {
			$buff = str_replace("%%has_menu%%", "false", $buff);
			$buff = str_replace("%%choices%%", "", $buff);
		}

		
//		$choices = "";
//		for ($i = 0; $i < count($this->signOffDesc[0]['choices']); $i++) {
//			if ($i > 0)
//				$choices .= ", ";
//			$choices .= "'" . $this->signOffDesc[0]['choices'][$i] . "'";
//		}
		//$buff = str_replace("%%choices%%", $choices, $buff);
		
		


		return($buff);
	}	
}

if ($_GET['test'] == "StepSignOffComp") {
	require("../pages/Page.php");
	
	class SignOffCompTestPage extends Page {
		
		function __construct() {
			parent::__construct("../templates/page_template.html");
		}
	
		protected function gen() {
			
			Page::replaceTag($this->pageBuff, "type", "");
			Page::replaceTag($this->pageBuff, "title", "Step Sign-Off Component Test");
			
			$signOff1 = new StepSignOffComp("SRP101", 1, 7);
			$content = $signOff1->toHtml();
			$content .= "<br /><br />";
			$signOff2 = new StepSignOffComp("SRP101", 2, 1);
			$content .= $signOff2->toHtml();
			
			Page::replaceTag($this->pageBuff, "content", $content);
		}
	}
	
	new SignOffCompTestPage();
}
?>