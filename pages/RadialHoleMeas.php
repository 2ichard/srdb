<?php


require("Page.php");

class RadialHoleMeas extends Page {
	
	private $seg = "unk";
	private $proc = "unk";
	private $ver = "unk";
	private $sec = -1;
	private $step = -1;

	
	function __construct($seg, $proc, $ver, $sec, $step) {
		$db = new SRDb();
		$this->seg = $seg;
		$this->proc = $proc;
		$this->ver = $ver;
		$this->sec = $sec;
		$this->step = $step;

		parent::__construct("../templates/radial_hole_meas_template.html");
	}

	public function gen() {
		$this->pageBuff = str_replace("%%seg%%", $this->seg, $this->pageBuff);
		$this->pageBuff = str_replace("%%proc%%", $this->proc, $this->pageBuff);
		$this->pageBuff = str_replace("%%ver%%", $this->ver, $this->pageBuff);
		$this->pageBuff = str_replace("%%sec%%", $this->sec, $this->pageBuff);
		$this->pageBuff = str_replace("%%step%%", $this->step, $this->pageBuff);
	}

	function __destruct() {
	}
}

if (!$_GET['seg'])
	throw new Exception("segment s/n not specified");
if (!$_GET['proc'])
	throw new Exception("procedure not specified");
if (!$_GET['ver'])
	throw new Exception("version not specified");
if (!$_GET['sec'])
	throw new Exception("section not specified");
if (!$_GET['step'])
	throw new Exception("step not specified");
	
new RadialHoleMeas($_GET['seg'], $_GET['proc'], $_GET['ver'], $_GET['sec'], $_GET['step']);
