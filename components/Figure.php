<?php

class Figure {
	
	private $num;
	private $imageFn;
	private $caption;
	private $figurePageURL;
	
	public function __construct($num, $imageFn, $caption) {
		$this->num = $num;
		$this->imageFn = $imageFn;
		$this->caption = $caption;
		$this->figurePageURL = "./FigurePage.php?caption=" . urlencode($caption) . 
			"&image=../{{content}}/images/" . $imageFn;
	}
	
	public function getFigureHtml() {
		$buff = file_get_contents("../templates/figure_template.html");
		$buff = str_replace("%%num%%", $this->num, $buff);
		$buff = str_replace("%%src%%", "../{{content}}/images/" . $this->imageFn, $buff);
		$buff = str_replace("%%caption%%", $this->caption, $buff);
		return($buff);
	}
	
	public function getThumbnailHtml() {
		$buff = file_get_contents("../templates/thumbnail_template.html");
		$buff = str_replace("%%num%%", $this->num, $buff);
		$buff = str_replace("%%thumbnail%%", "../{{content}}/images/thumbnails/" . $this->imageFn, $buff);
		//$buff = str_replace("%%link%%", "../content/images/" . $this->imageFn, $buff);
		$buff = str_replace("%%link%%", $this->figurePageURL, $buff);
		return($buff);
	}
	
	public function getLinkHtml() {
		$buff = file_get_contents("../templates/figure_link_template.html");
		$buff = str_replace("%%num%%", $this->num, $buff);
		//$buff = str_replace("%%link%%", "../content/images/" . $this->imageFn, $buff);
		$buff = str_replace("%%link%%", $this->figurePageURL, $buff);
		return($buff);
	}
}

?>