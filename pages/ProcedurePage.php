<?php
/*
Generate a segment procedure page given a procedure number, e.g. SRP114.

The name of the file containing the XML for the procedure is retrieved from the database.
*/

require("Page.php");
require("../lib/Procedure.php");
require("../translators/ProcedureXmlTranslator.php");

class ProcedurePage extends Page {
	
	private $fn;
	
	function __construct($num) {
		$db = new SRDb();
		$this->num = $num;
		if ($_GET['content'])
			$this->fn = "../" . $_GET['content'] . "/procedures/segment/" . $db->getProc($num)->getFn();
		else
			$this->fn = "../content/procedures/segment/" . $db->getProc($num)->getFn();

		parent::__construct("../templates/procedure_page_template.html");
	}

	public function gen() {
		try {
			$translator = new ProcedureXmlTranslator($this->fn);
		}
		catch(Exception $e) {
			$this->pageBuff = "Failed to load " . $this->num . ": " . $e->getMessage();
			return;
		}
		$this->pageBuff = str_replace("%%type%%", "Segment Procedure", $this->pageBuff);
		$title = $translator->getProcedure()->getNum() . "&nbsp;&nbsp;" . 
				$translator->getProcedure()->getTitle();
		$this->pageBuff = str_replace("%%title%%", $title, $this->pageBuff);
		$this->pageBuff = str_replace("%%version%%", $translator->getProcedure()->getVersion(), $this->pageBuff);
		$this->pageBuff = str_replace("%%content%%", $translator->getProcedure()->toHtml(), $this->pageBuff);		
	}

	function __destruct() {
	}
}

if ($_GET['test'] == "ProcedurePage") 
	new ProcedurePage("../srcdoc/SRB0000.xml");

else if ($_GET['type'] && $_GET['num']) 
	new ProcedurePage($_GET['num']);
	
else
	throw new Exception("invalid procedure spec");


