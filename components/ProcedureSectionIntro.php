<?php

class ProcedureSectionIntro {
	
	private $text;
	private $figure;
	
	function __construct($text, $figure) {
		$this->text = $text;
		$this->figure = $figure;
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/procedure_section_intro_template.html");
		$buff = str_replace("%%text%%", $this->text, $buff);
		if ($this->figure) {
			$buff = str_replace("%%figure%%", $this->figure->getThumbnailHtml(), $buff);
			$buff = str_replace("%%show_figure%%", true, $buff);

		}
		else {
			$buff = str_replace("%%show_figure%%", false, $buff);
		}

		return($buff);
	}
}
?>