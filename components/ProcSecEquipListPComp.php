<?php

require ("ProcSecEquipListItemPComp.php");

class ProcSecEquipListPComp {
	
	private $title = "Tools and Equipment";
	private $anchorName = "tools_and_equipment";
	private $items;
	private $secNum;
	
	function __construct($secNum, $items) {
		$this->secNum = $secNum;
		$this->items = $items;
	}
	
	public function getTitle() {
		return($this->title);
	}
	
	public function getAnchorName() {
		return($this->anchorName);
	}
	
	public function toHtml() {
		if ($this->items == NULL)
			return("");
				
		$buff = file_get_contents("../templates/proc_sec_equip_list_template.html");
		
		$buff = str_replace("%%title%%", $this->title . " for Section " . $this->secNum, $buff);
		$buff = str_replace("%%anchor_name%%", $this->anchorName . "_" . $this->secNum, $buff);
		
		$itemBuff = "";
		for ($i = 0; $i < count($this->items); $i++)
				$itemBuff .= $this->items[$i]->toHtml();
			
		$buff = str_replace("%%items%%", $itemBuff, $buff);
	
		return($buff);
	}	
}
?>


