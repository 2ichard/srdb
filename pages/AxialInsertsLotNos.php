<?php


require("Page.php");

class AxialInsertsLotNos extends Page {
	
	private $seg = "unk";
	
	function __construct($seg) {
		$db = new SRDb();
		$this->seg = $seg;

		parent::__construct("../templates/axial_inserts_lot_nos_template.html");
	}

	public function gen() {
		$this->pageBuff = str_replace("%%seg%%", $this->seg, $this->pageBuff);
	}

	function __destruct() {
	}
}

if (!$_GET['seg'])
	throw new Exception("segment s/n not specified");
	
new AxialInsertsLotNos($_GET['seg']);
