<?php

class ProcedureTOCItem {
	
	private $title;
	private $anchorName;
	
	function __construct($title, $anchorName) {
		$this->title = $title;
		$this->anchorName = $anchorName;
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/procedure_toc_item_template.html");
		$buff = str_replace("%%title%%", $this->title, $buff);
		$buff = str_replace("%%anchor_name%%", $this->anchorName, $buff);
		return($buff);
	}
}

if ($_GET['test'] == "ProcedureTOCItem") {
	$item = new ProcedureTOCItem("Foo Title", "FooTitle");
	print($item->toHtml());
}
?>