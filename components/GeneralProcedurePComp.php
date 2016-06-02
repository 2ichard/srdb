<?php

class GeneralProcedurePComp {
	
	private $num;
	private $title;
	private $link;
	
	public function __construct($num) {
		$this->num = $num;
		
		$db = new SRDb();
		$procDesc = $db->getProc($this->num);
		$this->title = $procDesc->getTitle();
		$this->link = "javascript:openProcedure('General', '" . $this->num . "')";
	}
	
	public function getNum() {
		return(strtoupper($this->num));
	}
	
	public function getLinkHtml() {
		$buff = file_get_contents("../templates/general_procedure_link_template.html");
		$buff = str_replace("%%num%%", $this->num, $buff);
		$buff = str_replace("%%title%%", $this->title, $buff);
		$buff = str_replace("%%link%%", $this->link, $buff);
		return($buff);
	}
	
	public function getShortLinkHtml() {
		return("<a href=\"" . $this->link . "\">" . $this->num . "</a>");
	}
	
	public function getListItemHtml() {
		return("<tr><td>" . $this->getLinkHtml() . "</td></tr>");
	}
}

?>