<?php

require("Page.php");


class FigurePage extends Page {
	
	private $caption;
	private $image;
	
	function __construct($caption, $image) {
		$this->caption = $caption;
		$this->caption = str_replace('\"', '"', $this->caption);
		$this->image = $image;
		parent::__construct("../templates/figure_page_template.html");
	}

	public function gen() {
		$this->pageBuff = str_replace("%%caption%%", $this->caption, $this->pageBuff);
		$this->pageBuff = str_replace("%%title%%", $this->caption, $this->pageBuff);
		$this->pageBuff = str_replace("%%image%%", $this->image, $this->pageBuff);		
	}
	


	function __destruct() {
	}
}

print($_GET['url']);
if ($_GET['caption'] && $_GET['image']) 
	new FigurePage($_GET['caption'], $_GET['image']);
else if ($_GET['image'])
	new FigurePage("", $_GET['image']);
else
	throw new Exception("invalid figure spec");

?>
