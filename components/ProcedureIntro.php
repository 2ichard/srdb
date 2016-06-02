<?php

class ProcedureIntro {
	
	private $text;
	private $figure;
	private $anchorName = "procedure_intro";
	
	function __construct($text, $figure) {
		$this->text = $text;
		$this->figure = $figure;
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/procedure_intro_template.html");
		$buff = str_replace("%%anchor_name%%", $this->anchorName, $buff);
		$buff = str_replace("%%text%%", $this->text, $buff);
		if ($this->figure)
			$buff = str_replace("%%figure%%", $this->figure->getFigureHtml(), $buff);
		else
			$buff = str_replace("%%figure%%", "", $buff);
		return($buff);
	}
	
	public function getAnchorName() {
		return($this->anchorName);
	}
}
?>