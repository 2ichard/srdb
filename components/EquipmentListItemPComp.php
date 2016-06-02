<?php

class SecEquipListItemPComp {
	
	private $num;
	private $text;
	
	function __construct($num, $text) {
		$this->num = $num;
		$this->text = $text;
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/equipment_list_item_template.html");
		$buff = str_replace("%%num%%", $this->num . ".", $buff);
		$buff = str_replace("%%text%%", $this->text, $buff);
		return($buff);
	}
}

if ($_GET['test'] == "EquipmentListItem") {
	$item = new EquipmentListItem("6", "Thousands of things, hundreds for each whiffletree.");
	print($item->toHtml());
}
?>