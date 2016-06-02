<?php

require("Page.php");
require("../lib/Procedure.php");
require("../translators/ProcedureXmlTranslator.php");

class GeneralProcedurePage extends Page {
	
	private $fn;
	
	function __construct($num) {
		$db = new SRDb();
		if ($_GET['content'])
			$this->fn = "../" . $_GET['content'] . "/procedures/general/" . $db->getProc($num)->getFn();
		else
			$this->fn = "../content/procedures/general/" . $db->getProc($num)->getFn();

		parent::__construct("../templates/general_procedure_page_template.html");
	}

	public function gen() {
		
		$this->pageBuff = str_replace("%%procedure_url%%", $this->fn, $this->pageBuff);
	}

	function __destruct() {
	}
}


if ($_GET['num']) 
	new GeneralProcedurePage($_GET['num']);
	
else
	throw new Exception("invalid procedure spec");


