<?php
//require ("../lib/SRDb.php");
class SignOffList {
	
	function __construct() {
	}
	
	public static function check($num, $version, $signOffs) {
		
		$db = new SRDb();
		
		$existingSignOffs = $db->getSignOffList($num, $version);
		
		
		if (count($existingSignOffs) == 0) {
			foreach($signOffs as $signOff) {
				try {
					$db->addSignOffListItem($num, $version, $signOff['section'], $signOff['step'], $signOff['type']);
				}
				catch(Exception $e) {
					throw new Exception("failed to add sign-off to list: " . $e->getMessage());
				}
			}
			return;
		}
		
		if (count($existingSignOffs) != count($signOffs))
			throw new Exception("inconsistent number of sign-offs, previously " .
				count($existingSignOffs) . ", now " . count($signOffs));
								
		foreach($signOffs as $signOff) {
			$found = FALSE;
			foreach($existingSignOffs as $existingSignOff) {
				if ($signOff['section'] == $existingSignOff['section']) {
					if ($signOff['step'] == $existingSignOff['step']) {
						if(strcasecmp($signOff['type'], $existingSignOff['type']) === 0) {
							$found = TRUE;
							break;
						}
					}
				}
			}
			if ($found === FALSE) 
				throw new Exception("inconsistent sign-off list, section " . $signOff['section'] .
					" step " . $signOff['step'] . " type " . $signOff['type'] . " not found in database");
		}
	}
}

?>