<?php

class SignOff {
	
	private $db;
	
	public $type;
	public $seg;
	public $time;
	public $who;
	public $proc;
	public $sec;
	public $step;
	
	public function __construct() {
		
		$this->db = new SRDb();
		
		$this->type = "unknown";
		$this->seg = 0;
		$this->t = 0;
		$this->who = "unknown";
		$this->proc = 0;
		$this->sec = 0;
		$this->step = 0;
	}
	
	public function save() {
		
		$this->db->addSignOff($this->type, $this->seg, $this->who, $this->proc, 
		$this->sec, $this->step);
		
	}
}


if ($_GET['cmnd'] == "save_signoff") {
	
	require("SRDb.php");

	$signOff = new SignOff();
	
	if ($_GET['type'])
		$signOff->type = $_GET['type'];
	else {
		print("error: missing type<br>\n");
		exit;
	}
		
	if ($_GET['seg'])
		$signOff->seg = $_GET['seg'];	
	else {
		print("error: missing seg<br>\n");
		exit;
	}
			
	if ($_GET['who'])
		$signOff->who = $_GET['who'];	
	else {
		print("error: missing who<br>\n");
		exit;
	}
			
	if ($_GET['proc'])
		$signOff->proc = $_GET['proc'];
	else {
		print("error: missing proc<br>\n");
		exit;
	}
			
	if ($_GET['sec'])
		$signOff->sec = $_GET['sec'];
	else {
		print("error: missing sec<br>\n");
		exit;
	}
			
	if ($_GET['step'])
		$signOff->step = $_GET['step'];	
	else {
		print("error: missing step<br>\n");
		exit;
	}
			
	$signOff->save();
}
?>