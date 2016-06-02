<?php

//require("./Step.php");

class SRDb { 
	
	private static $link;
	private $signoff_table = "signoff";
	private $signoff_desc_table = "signoff_desc";
	private $signoff_list_table = "signoff_list";
	private $proc_table = "procedures";
	private $tech_table = "techs";
	private $seg_table = "segs";
	private $axial_inserts_table = "axial_inserts";
	private $installed_lot_no_table = "installed_lot_nos";
	private $lots_table = "lots";
	private $dot_weights_table = "dot_weights";
	private $alignment_fixture_ids_table = "alignment_fixture_ids";
	private $procedure_notes_table = "procedure_notes";
	private $radial_hole_meas_table = "radial_hole_meas";
	private $radial_post_cent_table = "radial_post_cent";
	private $etch_soln_usage_table = "etch_soln_usage";
	private $flex_rod_runout_table = "flex_rod_runout";
	private $axial_hole_meas_table = "axial_hole_meas";

	function __construct() {
			
//		if (strcasecmp(gethostname(), "acsserver") === 0) {
//			$this->signoff_table .= "_dev";
//			$this->signoff_desc_table .= "_dev";
//			$this->signoff_list_table .= "_dev";
//			$this->proc_table .= "_dev";
//			$this->tech_table .= "_dev";
//			$this->seg_table .= "_dev";
//			$this->axial_inserts_table .= "_dev";
//			$this->installed_lot_no_table .= "_dev";
//			$this->lots_table .= "_dev";
//			$this->dot_weights_table .= "_dev";
//			$this->alignment_fixture_ids_table .= "_dev";
//			$this->procedure_notes_table .= "_dev";
//			$this->radial_hole_meas_table .= "_dev";
//			$this->radial_post_cent_table .= "_dev";
//			$this->etch_soln_usage_table .= "_dev";
//			$this->flex_rod_runout_table .= "_dev";
//			$this->axial_hole_meas_table .= "_dev";
//		}

		if (!self::$link) {
		if ((self::$link = mysql_connect('mysqlserver', 'segadmin', '2cseg')) == FALSE)
			throw new Exception('Could not connect: ' . mysql_error());
			
		if ((mysql_select_db('seg')) == FALSE)
			throw new Exception('Could not connect to seg database');
		}
	}
	
	function __destruct() {
		//if (self::$link)
		//	mysql_close(self::$link);
	}
	
	/*
	* Procedure access
	*/
	public function addProc($id, $num, $seq, $type, $title, $fn) {
		if (!$id || ($id === 0)) {		
			$query = sprintf("replace into %s (num, seq, type, title, filename) ", $this->proc_table);
			$query .= sprintf("values (\"%s\", %d, \"%s\", \"%s\", \"%s\")", $num, $seq, $type, $title, $fn);
		}
		else {
			$query = sprintf("replace into %s (id, num, seq, type, title, filename) ", $this->proc_table);
			$query .= sprintf("values (%d, \"%s\", %d, \"%s\", \"%s\", \"%s\")", 
				$id, $num, $seq, $type, $title, $fn);
		}
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: insert failed: " . $query);
		return($this->getLastId());
	}
		
	public function getProcs($type) {
		$query = sprintf("select * from %s ", $this->proc_table);
		if (strcasecmp($type, "all") !== 0)
			$query .= sprintf("where type = \"%s\"", $type);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: query failed: " . $query);
		$procs = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$procs[] = array('id' => $line['id'], 'num' => $line['num'], 'type' => $line['type'], 
					'title' => $line['title'], 'order' => $line['seq'], 'filename' => $line['filename']);
		}
		return($procs);
	}	
	
	public function getProcsJSON($type) {
		$procs = $this->getProcs($type);
		print("[");
		foreach ($procs as $p) {
			if ($p !== reset($procs)) print(", ");
			printf("{\"id\": %d, \"num\": \"%s\", \"type\": \"%s\", 
				\"title\": \"%s\", \"order\": %d, \"filename\": \"%s\"} ",
				$p['id'], $p['num'], $p['type'], $p['title'], $p['order'], $p['filename']);
		}
		print("]");
	}
		
	public function deleteProc($id) {
		$query = sprintf("delete from %s where id = %d", $this->proc_table, $id);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: delete failed: " . $query);
		return;
	}
	
	public function getProc($num) {
		$query = sprintf("select * from %s where num = \"%s\"", $this->proc_table, $num);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: query failed: " . $query);
		$line = mysql_fetch_array($result, MYSQL_ASSOC);
		return(new Procedure($line['num'], $line['type'], $line['title'], $line['p_order'], 
				$line['filename']));
	}
	
	// techs
	
	public function addTech($name) {
		$query = sprintf("select * from %s where name = \"%s\"", $this->tech_table, $name);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: query failed: " . $query);
		if (mysql_num_rows($result) > 0)
			return;
		$query = sprintf("insert into %s (name, type) values (\"%s\", \"regular\")", $this->tech_table, $name);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: insert failed: " . $query);
		return($this->getLastId($this->seg_table));	
	}
	
	public function deleteTech($name) {
		$query = sprintf("delete from %s where name = \"%s\"", $this->tech_table, $name);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: insert failed: " . $query);
	}
	
	public function getTechsJSON() {
		$query = sprintf("select * from %s order by name", $this->tech_table);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: query failed: " . $query);
		print("[");
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			if ($i > 0) print(", ");
			printf("{\"name\": \"%s\", \"type\": \"%s\"} ", $line['name'], $line['type']);
		}
		print("]");
	}
	
	// segs
	
	public function getSegsJSON($type) {
		if ($type == "all")
			$query = sprintf("select * from %s", $this->seg_table);
		else
			$query = sprintf("select * from %s where type=%d", $this->seg_table, $type);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: query failed: " . $query);
		print("[");
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			if ($i > 0) print(", ");
			printf("{\"id\": %d, \"sn\": %d, \"type\": \"%d\"} ", $line['id'], $line['sn'], $line['type']);
		}
		print("]");
	}
	
	public function addSeg($id, $type, $sn) {
				
		if (!$id || ($id === 0))		
			$query = sprintf(
				"replace into %s (type, sn) values (%d, %d)",  $this->seg_table, $type, $sn);
		else
			$query = sprintf(
				"replace into %s (id, type, sn) values (%d, %d, %d)", $this->seg_table, $id, $type, $sn);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: replace failed: " . $query);
			
		return($this->getLastId($this->seg_table));
	}
	
	
	public function deleteSeg($id) {
		$query = sprintf("delete from %s where id = %d", $this->seg_table, $id);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("delete failed: " . $query);
		return;
	}
			
	// sign-offs
	
	public function addSignOff($type, $seg, $tech, $proc, $ver, $sec, $step, $value, $notes) {
		// note that if sign-off is for batch procedure, the batch/lot number is in $seg
		switch($type) {
			case "axial_inserts":
			case "schott_etch":
			case "flex_rod_create":
				$this->updateLot($seg, "q_init", $value);
				$this->updateLot($seg, "q_now", $value);
				$this->updateLot($seg, "status", "Active");
		}
		if ($this->signOffExists($seg, $proc, $ver, $sec, $step) === true)
			throw new Exception("already signed-off");
		$query = sprintf("insert into %s (type, seg, tech, proc, version, sec, step, val, notes) ", 
				$this->signoff_table);
		$query .= sprintf("values (\"%s\", \"%s\", \"%s\", \"%s\", \"%s\", %d, %d, \"%s\", \"%s\")", 
				$type, $seg, $tech, $proc, $ver, $sec, $step, $value, $notes);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->signoff_table));
	}
		
	public function signOffExists($seg, $proc, $ver, $sec, $step) {
		$query = sprintf("select id from %s ", $this->signoff_table);
		$query .= sprintf("where seg = \"%s\" and proc = \"%s\" and version = \"%s\" and sec = %d and step = %d",
			$seg, $proc, $ver, $sec, $step);
		if (($result = mysql_query($query)) === FALSE) 
			throw new Exception("query failed: " . $query);
		if (mysql_num_rows($result) > 0)
			return(true);
		else
			return(false);
	}
	
	public function getSignOffsJSON($proc, $ver, $seg) {
		$query = sprintf("select * from %s ", $this->signoff_table);
		if (strcasecmp($proc, "all") || strcasecmp($ver, "all") || strcasecmp($seg, "all")) {
			$first = true;
			$query .= "where ";
			if (strcasecmp($proc, "all")) {
				if ($first)
					$first = false;
				else
					$query .= "and ";
				$query .= sprintf("proc = \"%s\" ", $proc);
			}
			if (strcasecmp($ver, "all")) {
				if ($first)
					$first = false;
				else
					$query .= "and ";
				$query .= sprintf("version = \"%s\" ", $ver);
			}
			if (strcasecmp($seg, "all")) {
				if ($first)
					$first = false;
				else
					$query .= "and ";
				$query .= sprintf("seg = \"%s\" ", $seg);
			}
		}
		$query .= " order by t desc";
				
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		
		print("[");
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			if ($i > 0) print(", ");
				printf("{\"id\": \"%d\", \"type\": \"%s\", \"seg\": \"%s\", ", 
						$line['id'], $line['type'], $line['seg']);
				printf("\"timestamp\": \"%s\", \"tech\": \"%s\", \"proc\": \"%s\", \"ver\": \"%s\", ", 
						$line['t'], $line['tech'], $line['proc'], $line['version']);
				printf("\"sec\": %d, \"step\": %d, \"substep\": %d, ", 
						$line['sec'], $line['step'], $line['substep']);
				printf("\"part\": \"%s\", \"value\": \"%s\", \"notes\": \"%s\"}", 
						$line['part'], $line['val'], $line['notes']);
		}
		print("]");
	}
	
	// sign-off descriptions 

	public function addSignOffDesc($id, $type, $title, $label, $valType, $minVal, $maxVal, $choices) {
		$query = sprintf("replace into %s ", $this->signoff_desc_table);
	
		if (!$id || ($id === 0)) {		
			$query .= sprintf("(type, title, label, valType, minVal, maxVal, choices) ");
			$query .= sprintf("values (\"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\")", 
					$type, $title, $label, $valType, $minVal, $maxVal, $choices);
		}
		else {
			$query .= sprintf("(id, type, title, label, valType, minVal, maxVal, choices) ");
			$query .= sprintf("values (%d, \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\")", 
					$id, $type, $title, $label, $valType, $minVal, $maxVal, $choices);
		}
		print($query);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("replace failed: " . $query);
		return($this->getLastId($this->signoff_desc_table));
	}
	
	public function deleteSignOffDesc($id) {
		$query = sprintf("delete from %s where id = %d", $this->signoff_desc_table, $id);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("delete failed: " . $query);
		return;
	}
	
	public function getSignOffDesc($type) {
		$query = sprintf("select * from %s ", $this->signoff_desc_table);
		if (strcasecmp($type, 'all') !== 0)
			$query .= sprintf("where type = \"%s\"", $type);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("error: query failed: " . $query);
		$descs = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$desc = array('id' => $line['id'], 'type' => $line['type'], 'title' => $line['title'], 
					'label' => $line['label'], 'valType' => $line['valType'], 
					'minVal' => $line['minVal'], 'maxVal' => $line['maxVal'], 'choices' => array());
			$choices = explode(',', $line['choices']);
			for ($c = 0; $c < count($choices); $c++) 
				$desc['choices'][$c] = $choices[$c];
			$descs[] = $desc;
		}
		return($descs);
	}
	
	public function getSignoffDescJSON($type) {
		$descs = $this->getSignOffDesc($type);
		print("[");
		foreach ($descs as $d) {
			if ($d !== reset($descs)) print(", ");
			printf("{\"id\": %d, \"type\": \"%s\", \"title\": \"%s\", \"label\": \"%s\",  ", 
				$d['id'], $d['type'], $d['title'], $d['label']);
			printf("\"valType\": \"%s\", \"minVal\": \"%s\", \"maxVal\": \"%s\", \"choices\": [", 
				$d['valType'], $d['minVal'], $d['maxVal']);	
			for ($c = 0; $c < count($d['choices']); $c++) {
				if ($c > 0) print(", ");
				print("\"" . $d['choices'][$c] . "\"");
			}
			print("] }");
		}
		print("]");
	}
	
	public function getAllSignOffs() {
		$signoffs = array();
		$query = sprintf("select * from %s", $this->signoff_table);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$signoff = new SignOff();
			$signoff->type = $line['type'];
			$signoff->seg = $line['seg'];
			$signoff->t = $line['t'];
			$signoff->who = $line['who'];
			$signoff->proc = $line['proc'];
			$signoff->sec = $line['sec'];
			$signoff->step = $line['step'];

			$signoffs[] = $signoff;
		}
		return($signoffs);
	}
	

	public function getSignOff($seg, $proc, $sec, $step) {
		$query = sprintf("select * from %s ", $this->signoff_table);
		$query .= sprintf("where seg = \"%s\" ", $seg);
		$query .= sprintf("and proc = %d ", $proc);
		$query .= sprintf("and sec = %d ", $sec);
		$query .= sprintf("and step = %d", $step);
		
		if (($result = mysql_query($query)) === FALSE) 
			throw new Exception("query failed: " . $query);
						
		$line = mysql_fetch_array($result, MYSQL_ASSOC);
		
		require("SignOff.php");
		$signOff = new SignOff();
		$signOff->type = $line['type'];
		$signOff->seg = $line['seg'];
		$signOff->t = $line['t'];
		$signOff->who = $line['who'];
		$signOff->proc = $line['proc'];
		$signOff->sec = $line['sec'];
		$signOff->step = $line['step'];

		return($signOff);
	}
	
	// sign-off list (list of sign-offs for a given procedure)
	
	public function getSignOffList($proc, $ver) {
		if (($proc == NULL) && ($ver == NULL))
			$query = sprintf("select * from %s order by section, step", $this->signoff_list_table);
		else
			$query = sprintf("select * from %s where proc = \"%s\" and version = \"%s\" order by section, step", 
				$this->signoff_list_table, $proc, $ver);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			// get sign-off title and label for convenience here
			$desc = $this->getSignOffDesc($line['type']);
			$resp[] = array(
					"id" => $line['id'],
					"proc" => $line['proc'],
					"version" => $line['version'],
					"section" => $line['section'],
					"step" => $line['step'],
					"type" => $line['type'],
					"title" => $desc[0]['title'],
					"label" => $desc[0]['label']);
		}
		return($resp);
	}
	
	public function getSignOffListJSON($proc, $ver) {
		$signOffs = $this->getSignOffList($proc, $ver);
		print("[");
		for ($i = 0; $i < count($signOffs); $i++) {
			if ($i > 0) print(", ");
			printf("{\"id\": %d, \"proc\": \"%s\", \"version\": \"%s\", \"section\": %d, 
				\"step\": %d, \"type\": \"%s\", \"title\": \"%s\", \"label\": \"%s\"}", 
				$signOffs[$i]['id'], $signOffs[$i]['proc'], $signOffs[$i]['version'], $signOffs[$i]['section'], 
				$signOffs[$i]['step'], $signOffs[$i]['type'], $signOffs[$i]['title'], $signOffs[$i]['label']);
		}
		print("]");
	}
	
	public function signOffListItemExists($proc, $ver, $section, $step) {
		$query = sprintf("select * from %s ", $this->signoff_list_table);
		$query .= sprintf(" where proc = \"%s\" and version = \"%s\" and section = %d and step = %d",
			$proc, $ver, $section, $step);
		if (($result = mysql_query($query)) === FALSE) 
			throw new Exception("query failed: " . $query);
		if (mysql_num_rows($result) > 0)
			return(true);
		else
			return(false);
	}
	
	public function addSignOffListItem($proc, $ver, $section, $step, $type, $override=false) {
		if ($this->signOffListItemExists($proc, $ver, $section, $step)) {
			if ($override) {
				$query = sprintf("delete from %s where ", $this->signoff_list_table);
				$query .= sprintf("proc=\"%s\" and version=\"%s\" and section=%d and step=%d",
						$proc, $ver, $section, $step);
				if (($result = mysql_query($query)) === FALSE)
					throw new Exception("override delete failed: " . $query);
			}
			else {
				throw new Exception("sign-off list item exists and override is false: " .
					$proc . " ver " . $version . " section " . $section . " step " . $step);
			}
		}
		$query = sprintf("insert into %s (proc, version, section, step, type) ", 
				$this->signoff_list_table);
		$query .= sprintf("values (\"%s\", \"%s\", %d, %d, \"%s\")", 
				$proc, $ver, $section, $step, $type);
		
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
							
		return($this->getLastId($this->signoff_list_table));
	}
	
	// installed lot numbers (axial inserts, radial pads)
	
	public function updateInstalledLotNo($type, $seg, $pos, $lot) {
		$query = sprintf("insert into %s (type, seg, pos, lot) ", $this->installed_lot_no_table);
		$query .= sprintf("values (\"%s\", \"%s\", \"%s\", \"%s\")", $type, $seg, $pos, $lot);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->installed_lot_no_table));
	}
		
	public function getInstalledLotNos($type, $seg) {
		$query = sprintf("select * from %s where type = \"%s\" and seg = \"%s\" order by t", 
				$this->installed_lot_no_table, $type, $seg);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$resp[$line['pos']] = array("id" => $line['id'], "timestamp" => $line['t'], 
				"pos" => $line['pos'], "lot" => $line['lot']);
		}
		//print_r($resp);
		return($resp);
	}
	
	public function getInstalledLotNosJSON($type, $seg) {
		$lotNos = $this->getInstalledLotNos($type, $seg);
		print("[");
		foreach ($lotNos as $a) {
			if ($a !== reset($lotNos)) print(", ");
			printf("{\"id\": %d, \"timestamp\": \"%s\", \"pos\": \"%s\", \"lot\": \"%s\"}", 
				$a['id'], $a['timestamp'], $a['pos'], $a['lot']);
		}
		print("]");
	}
	
	// flex rod runout
	
	public function updateFlexRodRunout($lot, $designation, $runout) {
		$query = sprintf("insert into %s (lot, designation, runout) ", $this->flex_rod_runout_table);
		$query .= sprintf("values (\"%s\", \"%s\", %f)", $lot, $designation, $runout);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->flex_rod_runout_table));
	}
	
	public function getFlexRodRunout($lot) {
		$query = sprintf("select * from %s where lot = \"%s\" order by t", 
		$this->flex_rod_runout_table, $lot);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$resp[$line['designation']] = array("id" => $line['id'], "timestamp" => $line['t'], 
				"designation" => $line['designation'], "lot" => $line['lot'],
				"runout" => $line['runout']);
		}
		//print_r($resp);
		return($resp);
	}
	
	public function getFlexRodRunoutJSON($lot) {
		$data = $this->getFlexRodRunout($lot);
		print("[");
		foreach ($data as $d) {
			if ($d !== reset($data)) print(", ");
			printf("{\"id\": %d, \"timestamp\": \"%s\", \"designation\": \"%s\", \"lot\": \"%s\", \"wiffletree\": \"%s\", \"runout\": %f}", 
				$d['id'], $d['timestamp'], $d['designation'], $d['lot'], $d['wiffletree'], $d['runout']);
		}
		print("]");
	}
	
	// dot weights
	
	public function updateDotWeight($seg, $proc, $ver, $sec, $step, $name, $weight) {
		$query = sprintf("insert into %s (seg, proc, ver, sec, step, name, weight) ", $this->dot_weights_table);
		$query .= sprintf("values (\"%s\", \"%s\", \"%s\", %d, %d, \"%s\", %f)", 
			$seg, $proc, $ver, $sec, $step, $name, $weight);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->dot_weights_table));
	}		
			
	public function getDotWeights($seg, $proc, $ver, $sec, $step) {
		$query = sprintf("select * from %s where ", $this->dot_weights_table);
		$query .= sprintf("seg = \"%s\" and proc = \"%s\" and ver = \"%s\" and sec = %d and step = %d ", 
				$seg, $proc, $ver, $sec, $step);
		$query .= "order by t";
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$resp[$line['name']] = array("id" => $line['id'], "timestamp" => $line['t'],
					"name" => $line['name'], "weight" => $line['weight']);
		}
		return($resp);
	}	
	
	public function getDotWeightsJSON($seg, $proc, $ver, $sec, $step) {
		$weights = $this->getDotWeights($seg, $proc, $ver, $sec, $step);
		print("[");
		foreach ($weights as $w) {
			if ($w !== reset($weights)) print(", ");
			printf("{\"id\": %d, \"timestamp\": \"%s\", \"name\": \"%s\", \"weight\": %f}", 
				$w['id'], $w['timestamp'], $w['name'], $w['weight']);
		}
		print("]");
	}
	
	// radial hole measurements
	
	public function updateRadialHoleMeas($seg, $proc, $ver, $sec, $step, $location, $meas_type, $meas_val) {
		$query = sprintf("select meas_val from %s where seg = \"%s\" and location = %d and meas_type = \"%s\" order by t",
			$this->radial_hole_meas_table, $seg, $location, $meas_type);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("select failed: " . $query);
		$result = mysql_query($query);
		$prev_val = -1.0;
		if (mysql_num_rows($result)) {
			for ($i = 0; $i < mysql_num_rows($result); $i++) {
				$line = mysql_fetch_array($result, MYSQL_ASSOC);
				$prev_val = $line['meas_val'];
			}
			print("prev val = " . $prev_val);
			if ($prev_val == $meas_val)
				return;
		}
		
		$query = sprintf("insert into %s ", $this->radial_hole_meas_table);
		$query .= sprintf("(seg, proc, ver, sec, step, location, meas_type, meas_val) ");
		$query .= sprintf("values (\"%s\", \"%s\", \"%s\", %d, %d, \"%s\", \"%s\", %f)", 
				$seg, $proc, $ver, $sec, $step, $location, $meas_type, $meas_val);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->radial_hole_meas_table));
	}		
	
	public function getRadialHoleMeas($seg) {
		$query = sprintf("select * from %s where seg = \"%s\" order by t", $this->radial_hole_meas_table, $seg);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			//print_r($line);
//			print("<br />");
			if (!$resp[$line['location']])
				 $resp[$line['location']] = array("location" => $line['location'], 
					"A" => -999.9, "B" => -999.9, "D" => -999.9, "F" => -999.9);		
			switch($line['meas_type']) {
				case "A":
					$resp[$line['location']]['A'] = $line['meas_val'];
					break;
				case "B":
					$resp[$line['location']]['B'] = $line['meas_val'];
					break;
				case "D":
					$resp[$line['location']]['D'] = $line['meas_val'];
					break;
				case "F":
					$resp[$line['location']]['F'] = $line['meas_val'];
					break;
			}
		}
		return($resp);
	}	

	public function getRadialHoleMeasJSON($seg) {
		$meas = $this->getRadialHoleMeas($seg);
		print("[");
		foreach ($meas as $m) {
			if ($m !== reset($meas)) print(", ");
			printf("{\"location\": %d, \"A\": %f, \"B\": %f, \"D\": %f, \"F\": %f}", 
				$m['location'], $m['A'], $m['B'], $m['D'], $m['F']);
		}
		print("]");
	}


	// radial post centration
	
	public function updateRadialPostCent($seg, $proc, $ver, $sec, $step, $loc, $gap) {
		$query = sprintf("insert into %s ", $this->radial_post_cent_table);
		$query .= sprintf("(seg, proc, ver, sec, step, loc, gap) ");
		$query .= sprintf("values (\"%s\", \"%s\", \"%s\", %d, %d, %d, %f)", $seg, $proc, $ver, $sec, $step, $loc, $gap);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->radial_post_cent_table));
	}		
	
	public function getRadialPostCent($seg) {
		$query = sprintf("select * from %s where seg = \"%s\" order by t", $this->radial_post_cent_table, $seg);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$resp[$line['loc']] = array("id" => $line['id'], "timestamp" => $line['t'],
					"loc" => $line['loc'], "gap" => $line['gap']);
		}
		return($resp);
	}	

	public function getRadialPostCentJSON($seg) {
		$meas = $this->getRadialPostCent($seg);
		print("[");
		foreach ($meas as $m) {
			if ($m !== reset($meas)) print(", ");
			printf("{\"id\": %d, \"timestamp\": \"%s\", \"loc\": %d, \"gap\": %f}", 
				$m['id'], $m['timestamp'], $m['loc'], $m['gap']);
		}
		print("]");
	}
	
	// axial hole measurements
	
	public function updateAxialHoleMeas($seg, $proc, $ver, $sec, $step, $hole, $meas_type, $meas_val) {
		$query = sprintf("select meas_val from %s where seg = \"%s\" and hole = \"%s\" and meas_type = \"%s\" order by t",
			$this->axial_hole_meas_table, $seg, $hole, $meas_type);
		print($query);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("select failed: " . $query);
		$result = mysql_query($query);
		$prev_val = -1.0;
		if (mysql_num_rows($result)) {
			for ($i = 0; $i < mysql_num_rows($result); $i++) {
				$line = mysql_fetch_array($result, MYSQL_ASSOC);
				$prev_val = $line['meas_val'];
			}
			print("prev val = " . $prev_val);
			if ($prev_val == $meas_val)
				return;
		}
		
		$query = sprintf("insert into %s ", $this->axial_hole_meas_table);
		$query .= sprintf("(seg, proc, ver, sec, step, hole, meas_type, meas_val) ");
		$query .= sprintf("values (\"%s\", \"%s\", \"%s\", %d, %d, \"%s\", \"%s\", %f)", 
				$seg, $proc, $ver, $sec, $step, $hole, $meas_type, $meas_val);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->axial_hole_meas_table));
	}		
	
	public function getAxialHoleMeas($seg) {
		$query = sprintf("select * from %s where seg = \"%s\" order by t", $this->axial_hole_meas_table, $seg);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			
			if (!$resp[$line['hole']])
				 $resp[$line['hole']] = array("hole" => $line['hole'], 
					"pre_grind_depth" => "", "post_grind_depth" => "",
					"pre_etch_dia" => "", "post_etch_dia" => "");
					
			switch($line['meas_type']) {
				case "pre_grind_depth":
					$resp[$line['hole']]['pre_grind_depth'] = $line['meas_val'];
					break;
				case "post_grind_depth":
					$resp[$line['hole']]['post_grind_depth'] = $line['meas_val'];
					break;
				case "pre_etch_dia":
					$resp[$line['hole']]['pre_etch_dia'] = $line['meas_val'];
					break;
				case "post_etch_dia":
					$resp[$line['hole']]['post_etch_dia'] = $line['meas_val'];
					break;
			}
		}
		return($resp);
	}	

	public function getAxialHoleMeasJSON($seg) {
		$meas = $this->getAxialHoleMeas($seg);
		print("[");
		foreach ($meas as $m) {
			if ($m !== reset($meas)) print(", ");
			printf("{\"id\": %d, \"timestamp\": \"%s\", \"hole\": \"%s\",
				\"pre_grind_depth\": %f, \"post_grind_depth\": %f, \"pre_etch_dia\": %f, \"post_etch_dia\": %f}", 
				$m['id'], $m['timestamp'], $m['hole'], 
				$m['pre_grind_depth'], $m['post_grind_depth'], $m['pre_etch_dia'], $m['post_etch_dia']);
		}
		print("]");
	}
	
	
	// etch solution usage
	
	public function updateEtchSolnUsage($seg, $loc, $batch, $lot, $vol) {
		$query = sprintf("insert into %s ", $this->etch_soln_usage_table);
		$query .= sprintf("(seg, loc, batch, lot, vol) ");
		$query .= sprintf("values (\"%s\", \"%s\", %d, \"%s\", %d)", 
				$seg, $loc, $batch, $lot, $vol);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->radial_post_cent_table));
	}		
	
	public function getEtchSolnUsage($seg) {
		$query = sprintf("select * from %s where seg = \"%s\" order by t", $this->etch_soln_usage_table, $seg);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$resp[$i] = array("id" => $line['id'], "timestamp" => $line['t'],
					"loc" => $line['loc'], "batch" => $line['batch'], "lot" => $line['lot'], "vol" => $line['vol']);
		}
		return($resp);
	}	

	public function getEtchSolnUsageJSON($seg) {
		$soln = $this->getEtchSolnUsage($seg);
		print("[");
		foreach ($soln as $s) {
			if ($s !== reset($soln)) print(", ");
			printf("{\"id\": %d, \"timestamp\": \"%s\", \"loc\": \"%s\", \"batch\": %d, \"lot\": \"%s\", \"vol\": %d}", 
				$s['id'], $s['timestamp'], $s['loc'], $s['batch'], $s['lot'], $s['vol']);
		}
		print("]");
	}

	// alignment fixtures
	
	public function updateAlignmentFixtureId($seg, $proc, $ver, $sec, $step, $point, $fixture_id) {
		$query = sprintf("insert into %s (seg, proc, ver, sec, step, point, fixture_id) ", 
				$this->alignment_fixture_ids_table);
		$query .= sprintf("values (\"%s\", \"%s\", \"%s\", %d, %d, \"%s\", \"%s\")", 
			$seg, $proc, $ver, $sec, $step, $point, $fixture_id);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->alignment_fixture_ids_table));
	}
			
	public function getAlignmentFixtureIds($seg) {
		$query = sprintf("select * from %s where ", $this->alignment_fixture_ids_table);
		$query .= sprintf("seg = \"%s\"", $seg);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$resp[$line['point']] = array("id" => $line['id'], "timestamp" => $line['t'],
					"point" => $line['point'], "fixture_id" => $line['fixture_id']);
		}
		return($resp);
	}	
	
	public function getAlignmentFixtureIdsJSON($seg) {
		$ids = $this->getAlignmentFixtureIds($seg);
		print("[");
		foreach ($ids as $i) {
			if ($i !== reset($ids)) print(", ");
			printf("{\"id\": %d, \"timestamp\": \"%s\", \"point\": \"%s\", \"fixture_id\": \"%s\"}", 
				$i['id'], $i['timestamp'], $i['point'], $i['fixture_id']);
		}
		print("]");
	}

	// lots
	
	public function addLot($type, $num) {
		if ($this->lotExists($num)) 
			throw new Exception("lot exists: " . $num);
		$query = sprintf("insert into %s (type, lot) values (\"%s\", \"%s\")", 
				$this->lots_table, $type, $num);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->lots_table));
	}
	
	public function deleteLot($id) {
		$query = sprintf("delete from %s where id = %d", $this->lots_table, $id);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("delete failed: " . $query);
		return;
	}
	
	public function updateLot($lot, $what, $v) {
		if (!($this->lotExists($lot)))
			throw new Exception("lot " . $lot . " does not exist");
		$query = sprintf("update %s ", $this->lots_table);
		switch($what) {
			case "q_init": $query .= sprintf("set q_init = %d ", $v); break;
			case "q_now": $query .= sprintf("set q_now = %d ", $v); break;
			case "status": 
				if (strcasecmp($v, "active") == 0)
					$query .= sprintf("set status = \"Active\"");
				else if (strcasecmp($v, "inactive") == 0)
					$query .= sprintf("set status = \"Inactive\"");
				else
					throw new Exception("invalid lot status: " . $v);
				break;
			default:
				throw new Exception("unrecognized thing: " . $what);
		}
		$query .= sprintf("where lot = \"%s\"", $lot);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
	}

	public function getLots($type, $activeOnly = false) {
		$query = sprintf("select * from %s ", $this->lots_table);
		if ($type !== "all") $query .= sprintf("where type = \"%s\"", $type);
		if ($activeOnly === true) $query .= " and status = \"Active\"";
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		//print($query . "<br/>");
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$resp[] = array("id" => $line['id'], "timestamp" => $line['t'], "lot" => $line['lot'], 
				"type" => $line['type'],  "q_init" => $line['q_init'], "q_now" => $line['q_now'], 
				"status" => $line['status']);
		}
		return($resp);
	}
	
	public function getLotsJSON($type, $activeOnly = false) {
		$lots = $this->getLots($type, $activeOnly);
		print("[");
		for ($i = 0; $i < count($lots); $i++) {
			if ($i > 0) print(", ");
			printf("{\"id\": %d, \"timestamp\": \"%s\", \"lot\": \"%s\", 
				\"type\": \"%s\", \"q_init\": %d, \"q_now\": %d, \"status\": \"%s\"}", 
				$lots[$i]['id'], $lots[$i]['timestamp'], $lots[$i]['lot'], $lots[$i]['type'],
				$lots[$i]['q_init'], $lots[$i]['q_now'], $lots[$i]['status']);
		}
		print("]");
	}
	
	public function lotExists($lot) {
		$query = sprintf("select * from %s where lot=\"%s\"", $this->lots_table, $lot);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		if (mysql_num_rows($result) > 0) 
			return(true);
		else 
			return(false);
	}
	
	public function lotExistsJSON($lot) {
		if ($this->lotExists($lot))
			print("[ true ]");
		else
			print("[ false ]");
	}
	
	public function getNextLotJSON() {
		for ($i = 1; $i < 1000; $i++) {
			$lot = date("ymd") . "-" . $i;
			if (!$this->lotExists($lot)) {
				printf("[{\"lot\": \"%s\"}]", $lot);
				return;
			}
		}
		throw new Exception("error: no more lots: " . $lot);		
	}
	
	// procedure notes
	
	public function addProcNote($seg, $proc, $ver, $tech, $note) {
		//print("<br>" . $note . "<br>");
		$note = str_replace("\'", "'", $note);
		$note = str_replace("\\\"", "\"", $note);
		$note = str_replace("'", "\'", $note);
		$note = str_replace("\"", "\\\"", $note);
		//print("<br>" . $note . "<br>");
		$query = sprintf("insert into %s (seg, proc, ver, tech, note) ", $this->procedure_notes_table);
		$query .= sprintf("values (\"%s\", \"%s\", \"%s\", \"%s\", \"%s\")", $seg, $proc, $ver, $tech, $note);
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("insert failed: " . $query);
		return($this->getLastId($this->procedure_notes_table));
	}

	public function getProcNotes($proc, $ver, $seg) {
		$query = sprintf("select * from %s ", $this->procedure_notes_table);
		if (strtolower($proc) == "all") $allProc = TRUE; else $allProc = FALSE;
		if (strtolower($ver) == "all") $allVer = TRUE; else $allVer = FALSE;
		if (strtolower($seg) == "all") $allSeg = TRUE; else $allSeg = FALSE;
		
		if (!$allProc || !$allVer || !$allSeg) {
			$query .= "where ";
			if (!$allProc)
				$query .= sprintf("proc = \"%s\" ", $proc);
			if (!$allVer) {
				if (!$allProc) $query .= " and ";
				$query .= sprintf("ver = \"%s\" ", $ver);
			}
			if (!$allSeg) {
				if (!$allProc && !$allSeg) $query .= " and ";
				$query .= sprintf("seg = \"%s\" ", $seg);
			}
		}

		$query .= sprintf("order by t desc");
				
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("query failed: " . $query);
		$resp = array();
		for ($i = 0; $i < mysql_num_rows($result); $i++) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);
			$resp[] = array("id" => $line['id'], "timestamp" => $line['t'], "proc" => $line['proc'],
				"ver" => $line['ver'], "seg" => $line['seg'], "tech" => $line['tech'], "note" => $line['note']);
		}
		return($resp);
	}
		
	public function getProcNotesJSON($proc, $ver, $seg) {
		$notes = $this->getProcNotes($proc, $ver, $seg);
		
		print("[");
		for ($i = 0; $i < count($notes); $i++) {
			if ($i > 0) print(", ");
			printf("{\"id\": %d, \"timestamp\": \"%s\", \"proc\": \"%s\", \"ver\": \"%s\", ",
				$notes[$i]['id'], $notes[$i]['timestamp'], $notes[$i]['proc'], $notes[$i]['ver']);
			printf("\"seg\": \"%s\", \"tech\": \"%s\", \"note\": \"%s\"}",  
				$notes[$i]['seg'], $notes[$i]['tech'], str_replace("\"", "\\\"", $notes[$i]['note']));
		}
		print("]");
	}

	/* 
	** utility
	*/
	private function getLastId($table) {
		$query = "select max(id) from " . $table;
		if (($result = mysql_query($query)) === FALSE)
			throw new Exception("select failed: " . $query);
		if (mysql_num_rows($result) != 1)
			throw new Exception($query);
		$line = mysql_fetch_array($result, MYSQL_ASSOC);
		return($line['max(id)']);
	}
	
	public static function checkReqArgs($reqArgs) {
		foreach ($reqArgs as $arg)
			if (!$_GET[$arg])
				if ($_GET[$arg] != "0")
					throw new Exception('required arg ' . $arg . ' for ' . $_GET['dbcmnd'] . ' not present');
	}
}

//$db = NULL;

if ($_GET['dbcmnd']) {
	try {
		$db = new SRDb();
	}
	catch (Exception $e) {
		exit($e->getMessage());
	}
		
	try {	
		switch($_GET['dbcmnd']) {
			
			// sign-offs
			
			case "getSignOffsJSON":
				SRDb::checkReqArgs(array('proc', 'ver', 'seg'));
				$db->getSignOffsJSON($_GET['proc'], $_GET['ver'], $_GET['seg']);
				break;
				
			// sign-off list
			
			case "getSignOffList":
				SRDb::checkReqArgs(array('proc', 'ver'));
				$db->getSignOffList($_GET['proc'], $_GET['ver']);
				break;
				
			case "getSignOffListJSON":
				if (!$_GET['proc'] && !$_GET['ver'])
					$db->getSignOffListJSON(NULL, NULL);
				else {
					SRDb::checkReqArgs(array('proc', 'ver'));
					$db->getSignOffListJSON($_GET['proc'], $_GET['ver']);
				}
				break;
				
			case "addSignOffListItem":
				SRDb::checkReqArgs(array('proc', 'ver', 'section', 'step', 'type'));
				$db->addSignOffListItem(
					$_GET['proc'], $_GET['ver'], $_GET['section'], $_GET['step'], $_GET['type']);
				break;
				
			case "addSignOff":
			case "addsignoff":
				SRDb::checkReqArgs(array('type', 'seg', 'tech', 'proc', 'ver', 'sec', 'value', 'notes'));
					$db->addSignOff($_GET['type'], $_GET['seg'], $_GET['tech'], $_GET['proc'], $_GET['ver'], 
							$_GET['sec'], $_GET['step'], $_GET['value'], $_GET['notes']);
				break;
				
				
			// sign-off descriptions
						
			case "addSignOffDesc":
				SRDb::checkReqArgs(array('type', 'title', 'label', 'valType', 'minVal', 'maxVal', 'choices'));
				$db->addSignOffDesc($_GET['id'], $_GET['type'], $_GET['title'], $_GET['label'], $_GET['valType'], 
						$_GET['minVal'], $_GET['maxVal'], $_GET['choices']);
				break;
				
			case "deleteSignOffDesc":
				SRDb::checkReqArgs(array('id'));
				$db->deleteSignoffDesc($_GET['id']);
				break;
			
			case "getSignOffDescJSON":
				if (!$_GET['type']) 
					$db->getSignoffDescJSON('all');
				else
					$db->getSignoffDescJSON($_GET['type']);
				break;
			
			// segs
			
			case "getsegsjson":
			case "getSegsJSON":
				$db->getSegsJSON($_GET['type']);
				break;
				
			case "addseg":
				$db->addSeg($_GET['id'], $_GET['type'], $_GET['sn']);
				break;
			
			case "deleteseg":
				$db->deleteSeg($_GET['id']);
				break;
				
			// installed lot numbers (axial inserts, radial pads)
			
			case "updateInstalledLotNo":
				SRDb::checkReqArgs(array('type', 'seg', 'pos', 'lot'));
				$db->updateInstalledLotNo($_GET['type'], $_GET['seg'], $_GET['pos'], $_GET['lot']);
				break;
				
			case "getInstalledLotNos":
				SRDb::checkReqArgs(array('type', 'seg'));
				$db->getInstalledLotNos($_GET['type'], $_GET['seg']);
				break;
				
			case "getInstalledLotNosJSON":
				SRDb::checkReqArgs(array('type', 'seg'));
				$db->getInstalledLotNosJSON($_GET['type'], $_GET['seg']);
				break;
				
			// flex rod runout
			
			case "updateFlexRodRunout":
			SRDb::checkReqArgs(array('lot', 'designation', 'runout'));
			$db->updateFlexRodRunout($_GET['lot'], $_GET['designation'],  $_GET['runout']);
			break;
			
			case "getFlexRodRunoutJSON":
			SRDb::checkReqArgs(array('lot'));
			$db->getFlexRodRunoutJSON($_GET['lot']);
			break;
				
			// dot weights
			
			case "updateDotWeight":
				SRDb::checkReqArgs(array('seg', 'proc', 'ver', 'sec', 'step', 'name', 'weight'));
				$db->updateDotWeight($_GET['seg'], $_GET['proc'], $_GET['ver'], $_GET['sec'], 
					$_GET['step'], $_GET['name'], $_GET['weight']);
				break;
				
			case "getDotWeightsJSON":
				SRDb::checkReqArgs(array('seg', 'proc', 'ver', 'sec', 'step'));
				$db->getDotWeightsJSON($_GET['seg'], $_GET['proc'], $_GET['ver'], $_GET['sec'], $_GET['step']);			
				break;
	
			// radial hole measurements
			
			case "updateRadialHoleMeas":
				SRDb::checkReqArgs(array('seg', 'proc', 'ver', 'sec', 'step', 'meas_type', 'meas_val'));
				$db->updateRadialHoleMeas($_GET['seg'], $_GET['proc'], $_GET['ver'], $_GET['sec'], 
					$_GET['step'], $_GET['location'], $_GET['meas_type'], $_GET['meas_val']);
				break;
				
			case "getRadialHoleMeasJSON":
				SRDb::checkReqArgs(array('seg'));
				$db->getRadialHoleMeasJSON($_GET['seg']);			
				break;
				
			// radial post centration
			
			case "updateRadialPostCent":
				SRDb::checkReqArgs(array('seg', 'proc', 'ver', 'sec', 'step', 'loc', 'gap'));
				$db->updateRadialPostCent($_GET['seg'], $_GET['proc'], $_GET['ver'], $_GET['sec'], 
					$_GET['step'], $_GET['loc'], $_GET['gap']);
				break;
				
			case "getRadialPostCentJSON":
				SRDb::checkReqArgs(array('seg'));
				$db->getRadialPostCentJSON($_GET['seg']);			
				break;
				
				
			// axial hole measurements
			
			case "updateAxialHoleMeas":
				SRDb::checkReqArgs(array('seg', 'proc', 'ver', 'sec', 'step', 'hole', 'meas_type', 'meas_val'));
				$db->updateAxialHoleMeas($_GET['seg'], $_GET['proc'], $_GET['ver'], $_GET['sec'], 
					$_GET['step'], $_GET['hole'], $_GET['meas_type'], $_GET['meas_val']);
				break;
				
			case "getAxialHoleMeasJSON":
				SRDb::checkReqArgs(array('seg'));
				$db->getAxialHoleMeasJSON($_GET['seg']);			
				break;
				
			// etch solution usage
			
			case "updateEtchSolnUsage":
				SRDb::checkReqArgs(array('seg', 'loc', 'batch', 'lot', 'vol'));
				$db->updateEtchSolnUsage($_GET['seg'], 
					$_GET['loc'], $_GET['batch'], $_GET['lot'], $_GET['vol']);
				break;
				
			case "getEtchSolnUsageJSON":
				SRDb::checkReqArgs(array('seg'));
				$db->getEtchSolnUsageJSON($_GET['seg']);			
				break;
				
			// alignment fixture IDs
			
			case "updateAlignmentFixtureId":
				SRDb::checkReqArgs(array('seg', 'proc', 'ver', 'sec', 'step', 'point', 'fixture_id'));
				$db->updateAlignmentFixtureId($_GET['seg'], $_GET['proc'], $_GET['ver'], $_GET['sec'], 
					$_GET['step'], $_GET['point'], $_GET['fixture_id']);
				break;
				
			case "getAlignmentFixtureIdsJSON":
				SRDb::checkReqArgs(array('seg'));
				$db->getAlignmentFixtureIdsJSON($_GET['seg']);			
				break;
	
			// lots
			
			case "lotExistsJSON":
				SRDb::checkReqArgs(array('lot'));
				$db->lotExistsJSON($_GET['lot']);
				break;
				
			case "getLotsJSON":
				SRDb::checkReqArgs(array('type'));
				if ($_GET['activeOnly'] == "true")
					$db->getLotsJSON($_GET['type'], true);
				else
					$db->getLotsJSON($_GET['type']);
				break;
				
			case "addLot":
				SRDb::checkReqArgs(array('type', 'num'));
				$db->addLot($_GET['type'], $_GET['num']);
				break;
				
			case "updateLot":
				SRDb::checkReqArgs(array('lot', 'what', 'val'));
				$db->updateLot($_GET['lot'], $_GET['what'], $_GET['val']);
				break;
				
			case "getNextLotJSON":
				$db->getNextLotJSON();
				break;
				
			case "deleteLot":
				SRDb::checkReqArgs(array('id'));
				$db->deleteLot($_GET['id']);
				break;
	
				
			// procedure notes
			
			case "addProcNote":
				SRDb::checkReqArgs(array('seg', 'proc', 'ver', 'tech', 'note'));
				$db->addProcNote($_GET['seg'], $_GET['proc'], $_GET['ver'], $_GET['tech'], $_GET['note']);
				break;	
				
			case "getProcNotesJSON":
				SRDB::checkReqArgs(array('proc', 'ver', 'seg'));
				$db->getProcNotesJSON($_GET['proc'], $_GET['ver'], $_GET['seg']);
				break;	
			
			// techs
			
			case "addTech":
				SRDB::checkReqArgs(array('name'));
				$db->addTech($_GET['name']);
				break;
				
			case "techsjson":
			case "getTechsJSON":
				$db = new SRDb();
				$db->getTechsJSON();
				break;
				
			case "deleteTech":
				SRDB::checkReqArgs(array('name'));
				$db->deleteTech($_GET['name']);
				break;
				
			case "segprocsjson":
				$db->getProcsJSON("segment");
				break;
				
			case "getBatchProcsJSON":
				$db->getProcsJSON("batch");
				break;
				
			// procedures
			
			case "getProcsJSON":
				if (!$_GET['type'])
					$db->getProcsJSON("all");
				else
					$db->getProcsJSON($_GET['type']);
				break;
				
			case "addProc":
				SRDB::checkReqArgs(array('num', 'order', 'type', 'title', 'fn'));
				$db->addProc($_GET['id'], $_GET['num'], $_GET['order'], $_GET['type'], $_GET['title'], $_GET['fn']);
				break;
				
			case "deleteProc":
				SRDB::checkReqArgs(array('id'));
				$db->deleteProc($_GET['id']);
				break;
				
			default:
				exit("error: SRDb: unrecognized cmmd: " . $_GET['dbcmnd']);
		}
	}
	catch(Exception $e) {
		print("error: " . $e->getMessage());
	}
}

?>
