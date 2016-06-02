<?php


require("Page.php");

class RadialPadsLotNos extends Page {
	
	private $seg = "unk";
	
	function __construct($seg) {
		$db = new SRDb();
		$this->seg = $seg;

		parent::__construct("../templates/radial_pad_lot_nos_template.html");
	}

	public function gen() {
		$this->pageBuff = str_replace("%%seg%%", $this->seg, $this->pageBuff);
	}

	function __destruct() {
	}
}

if (!$_GET['seg'])
	throw new Exception("segment s/n not specified");
	
new RadialPadsLotNos($_GET['seg']);
