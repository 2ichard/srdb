<?php

class StepSignOffComp {

	private $proc;
	private $sec;
	private $step;
	
	function __construct($proc, $sec, $step) {
		$this->proc = $proc;
		$this->sec = $sec;
		$this->step = $step;
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/step_signoff_template_2.html");
		$buff = str_replace("%%proc%%", $this->proc, $buff);
		$buff = str_replace("%%ver%%", "1.0", $buff);
		$buff = str_replace("%%sec%%", $this->sec, $buff);
		$buff = str_replace("%%step%%", $this->step, $buff);
		
		$buff = str_replace("%%id%%", "sec" . $this->sec . "Step" . $this->step, $buff);

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