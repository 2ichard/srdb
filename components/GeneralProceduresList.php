<?php

//require("ProcedureTOCItem.php");

class GeneralProceduresList {
	
	private $generalProcedures;
	
	function __construct($generalProcedures) {
		$this->generalProcedures = $generalProcedures;
	}
	
	public function toHtml() {
			
		$buff = file_get_contents("../templates/general_procedures_list_template.html");
		
		if (count($this->generalProcedures) > 0) {
			$itemBuff = "";		
			for ($i = 0; $i < count($this->generalProcedures); $i++) {
				$itemBuff .= $this->generalProcedures[$i]->getListItemHtml();
			}
			$buff = str_replace("%%list%%", $itemBuff, $buff);
		}
		else
			$buff = str_replace("%%list%%", "<tr><td>None</td></tr>", $buff);
		
		return($buff);
	}	
}
?>


