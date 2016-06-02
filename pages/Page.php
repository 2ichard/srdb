<?php

require("../lib/SRDb.php");

// Base class of all pages.  Loads page template, instantiates database access object, 
// calls abstract function gen to construct the page and function write to emit the page.

abstract class Page  {

	// the page is built in this buffer
	protected $pageBuff;
	
	// database access object
	protected $db;
	
	
	function __construct($templateFn) {
		
		$this->db = new SRDb();
		
		// put template in page buff
		if (($this->pageBuff = file_get_contents($templateFn)) === FALSE)
			throw new Exception("error: can\'t load page template: " . $templateFn);
			
		// uncomment commented replacement tages
		// (commented replacement tags are used in some cases to hide
		// replacement tags from browser for display of raw templates)
		$this->pageBuff = str_replace("<!--%%", "%%", $this->pageBuff);
		$this->pageBuff = str_replace("%%-->", "%%", $this->pageBuff);
		
		// generate and emit the page
		$this->gen();
		print $this->pageBuff;
	}

	// generates page, subclasses much implement this function
	abstract protected function gen();

	static function replaceTag(&$buff, $tag, $replacement) {
		$buff = str_replace("%%" . $tag . "%%", $replacement, $buff);
	}
	
#	public function replaceTag($tag, $replacement) {
#		$this->pageBuff = str_replace("%%" . $tag . "%%", $replacement, $this->pageBuff);
#	}

	function __destruct() {
	}
}

// For unit test call this page with ?test=Page  
if ($_GET['test'] == 'Page') {

	class TestPage extends Page {
		function __construct() {
			parent::__construct("../templates/page_template.html");
		}

		function gen() {
			Page::replaceTag($this->pageBuff, "title", "Page Class Test");
		}
	}

	try {
		$page = new TestPage();
	}
	catch(Exception $e) {
		print $e->getMessage();
	}
}
