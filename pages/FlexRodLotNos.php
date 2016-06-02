<?php


require("Page.php");

class FlexRodLotNos extends Page {
	
	private $lot = "unk";
	
	function __construct() {
		$db = new SRDb();

		parent::__construct("../templates/flex_rod_lot_nos_template.html");
	}

	public function gen() {
		//$this->pageBuff = str_replace("%%lot%%", $this->lot, $this->pageBuff);
	}

	function __destruct() {
	}
}

//if (!$_GET['seg'])
//	throw new Exception("lot number not specified");
	
new FlexRodLotNos();
