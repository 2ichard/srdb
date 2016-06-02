<?php

//require("../lib/SRDb.php");

class DataEntryPComp {

	private $type;
	private $proc;
	private $version;
	private $sec;
	private $step;
	
	function __construct($type, $proc, $version, $sec, $step) {
		$this->type = $type;
		$this->proc = $proc;
		$this->version = $version;
		$this->sec = $sec;
		$this->step = $step;
		
		//$srdb = new SRDb();
		//$this->signOffDesc = $srdb->getSignoffDesc($type);
	}
	
	public function toHtml() {
		$buff = file_get_contents("../templates/data_entry_template.html");
		$buff = str_replace("%%proc%%", $this->proc, $buff);
		$buff = str_replace("%%ver%%", $this->version, $buff);
		$buff = str_replace("%%sec%%", $this->sec, $buff);
		$buff = str_replace("%%step%%", $this->step, $buff);

		switch($this->type) {
			
			case "axial_inserts":
				$buff = str_replace("%%title%%", "Axial Insert Lot Numbers", $buff);
				$buff = str_replace("%%base_url%%", "./AxialInsertsLotNos.php", $buff);
				break;
				
			case "radial_pads":
				$buff = str_replace("%%title%%", "Radial Pad Lot Numbers", $buff);
				$buff = str_replace("%%base_url%%", "./RadialPadLotNos.php", $buff);
				break;
				
			case "dot_weights":
				$buff = str_replace("%%title%%", "Adhesive Dot Weights", $buff);
				$buff = str_replace("%%base_url%%", "./DotWeights.php", $buff);
				break;
				
			case "alignment_fixture_ids":
				$buff = str_replace("%%title%%", "Alignment Fixture IDs", $buff);
				$buff = str_replace("%%base_url%%", "./AlignmentFixtureIds.php", $buff);
				break;
			
			case "radial_hole":
				$buff = str_replace("%%title%%", "Radial Hole Measurements", $buff);
				$buff = str_replace("%%base_url%%", "./RadialHoleMeas.php", $buff);
				break;
				
			case "radial_post":
				$buff = str_replace("%%title%%", "Radial Post Centration", $buff);
				$buff = str_replace("%%base_url%%", "./RadialPostCent.php", $buff);
				break;
				
			case "etch_soln_usage":
				$buff = str_replace("%%title%%", "Etch Solution Usage", $buff);
				$buff = str_replace("%%base_url%%", "./EtchSolnUsage.php", $buff);
				break;
				
			case "flex_rod_runout":
				$buff = str_replace("%%title%%", "Flex Rod Runout", $buff);
				$buff = str_replace("%%base_url%%", "./FlexRodRunout.php", $buff);
				break;
				
			case "flex_rod_lot_nos":
				$buff = str_replace("%%title%%", "Flex Rod Lot Numbers", $buff);
				$buff = str_replace("%%base_url%%", "./FlexRodLotNos.php", $buff);
				break;
				
			case "axial_hole_meas":
				$buff = str_replace("%%title%%", "Axial Hole Measurements", $buff);
				$buff = str_replace("%%base_url%%", "./AxialHoleMeas.php", $buff);
				break;
				
			default:
				$buff = str_replace("%%title%%", "Unknown Data Entry Type", $buff);
				break;	
		}
		

		return($buff);
	}	
}

?>