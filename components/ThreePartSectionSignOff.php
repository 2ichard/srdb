<?php

class ThreePartSectionSignOff {

	private $procNum;
	private $secNum;
	
	function __construct($procNum, $secNum) {
		$this->procNum = $procNum;
		$this->secNum = $secNum;
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/three_part_section_sign_off_template.html");
		$buff = str_replace("%%proc_num%%", $this->procNum, $buff);
		$buff = str_replace("%%sec_num%%", $this->secNum, $buff);
		return($buff);
	}	
}

?>