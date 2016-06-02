<?php
/*
Generate a batch procedure page given a procedure number, e.g. SRP114.

The name of the file containing the XML for the procedure is retrieved from the database.
*/

require("Page.php");
require("../lib/Procedure.php");
require("../translators/ProcedureXmlTranslator.php");

class BatchProcedurePage extends Page {
	
	private $fn;
	
	function __construct($num) {
		$db = new SRDb();
		if ($_GET['content'])
			$this->fn = "../" . $_GET['content'] . "/procedures/batch/" . $db->getProc($num)->getFn();
		else
			$this->fn = "../content/procedures/batch/" . $db->getProc($num)->getFn();

		parent::__construct("../templates/batch_procedure_page_template.html");
	}

	public function gen() {
		$translator = new ProcedureXmlTranslator($this->fn);
		$this->pageBuff = str_replace("%%type%%", "Batch Procedure", $this->pageBuff);
		$title = $translator->getProcedure()->getNum() . "&nbsp;&nbsp;" . 
				$translator->getProcedure()->getTitle();
		$this->pageBuff = str_replace("%%title%%", $title, $this->pageBuff);
		$this->pageBuff = str_replace("%%version%%", $translator->getProcedure()->getVersion(), $this->pageBuff);
		$this->pageBuff = str_replace("%%content%%", $translator->getProcedure()->toHtml(), $this->pageBuff);		
	}

	function __destruct() {
	}
}

if ($_GET['num']) 
	new BatchProcedurePage($_GET['num']);
	
else
	throw new Exception("invalid procedure spec");


