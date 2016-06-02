<?php

require ("EquipmentListItem.php");

class EquipmentList {
	
	private $title = "Tools and Equipment";
	private $anchorName = "tools_and_equipment";
	
	function __construct($items) {
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
				
		$buff = file_get_contents("../templates/equipment_list_template.html");
		
		$buff = str_replace("%%title%%", $this->title, $buff);
		$buff = str_replace("%%anchor_name%%", $this->anchorName, $buff);
		
		$itemBuff = "";
		for ($i = 0; $i < count($this->items); $i++)
				$itemBuff .= $this->items[$i]->toHtml();
			
		$buff = str_replace("%%items%%", $itemBuff, $buff);
	
		return($buff);
	}	
}

if ($_GET['test'] == "EquipmentList") {
	
	$items = array();
	$items[] = new EquipmentListItem("1", "First thing.");
	$items[] = new EquipmentListItem("2", "Second thing.");
	$items[] = new EquipmentListItem("3", "Integer blandit congue enim condimentum molestie. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque rutrum ornare nulla sed lobortis.");
	$items[] = new EquipmentListItem("100", "Second thing.");

	$list = new EquipmentList($items);
	print($list->toHtml());
}
?>


