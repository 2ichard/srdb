<?php

require "Page.php";
require "../lib/Procedure.php";

class SummaryPage extends Page {
	
	function __construct() {
		parent::__construct("../templates/status_summary_page_template.html");
	}
	
	protected function gen() {
		// SRDBPage::replaceTag($this->pageBuff, "title", "Launch Page Prototype");
		$procs = array();
		$procs = $this->db->getProcs("Segment");
		$rowBuff = "";
		for ($i = 0; $i < count($procs); $i++) 
			$rowBuff .= $this->constructStatusTableRow($procs[$i]);
		Page::replaceTag($this->pageBuff, "status_rows", $rowBuff);
	}
	
	private function constructStatusTableRow($proc) {
		$templateFn = "../templates/status_summary_row_template.html";
		if (($buff = file_get_contents($templateFn)) === FALSE)
			throw new Exception("error: can\'t load template: " . $templateFn);
		Page::replaceTag($buff, "proc_num", $proc->getNum());
		Page::replaceTag($buff, "proc_title", $proc->getTitle());
		return($buff);
	}
}

new SummaryPage();

?>