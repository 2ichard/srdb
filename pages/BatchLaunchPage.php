<?php

require "Page.php";
require "../lib/Procedure.php";

class BatchLaunchPage extends Page {
	
	function __construct() {
		parent::__construct("../templates/batch_launch_page_template.html");
	}
	
	protected function gen() {
		Page::replaceTag($this->pageBuff, "title", "SRDB Batch Launch");
	}
	
	private function constructProcTableRow($proc) {
		$templateFn = "../templates/launch_page_table_row_template.html";
		if (($buff = file_get_contents($templateFn)) === FALSE)
			throw new Exception("error: can\'t load template: " . $templateFn);
		Page::replaceTag($buff, "num", $proc->getNum());
		Page::replaceTag($buff, "title", $proc->getTitle());
		Page::replaceTag($buff, "link", "javascript:loadProcedure('" . $proc->getNum() . "', 'seg')");
		Page::replaceTag($buff, "started", "not yet started");
		Page::replaceTag($buff, "completed", "not yet completed");
		return($buff);
	}
}

new BatchLaunchPage();

?>