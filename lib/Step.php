<?php

class Step {
	
	public $id = -1;
	public $p_id = -1;
	public $p_order = 0;
	
	public $title = "uninitialized";
	public $checkable = false;
	public $loggable= false;
	public $qcable = false;
	public $hasLT = false;
	public $hasOtherData = false;
	public $hasTimeEstimates = false;
	public $baseHrs = 0.0;
	public $reserve = 0;
	public $maxHrs = 0.0;
	public $text = "";
	
	
	function __construct() {
	}
}

?>