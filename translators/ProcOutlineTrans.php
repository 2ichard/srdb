<?php
require ("../lib/SRDb.php");

/*
To make the csv file properly readable, I had to %s/^V^M/\r/g
*/

class ProcOutlineTrans {

	private $db;
	private $fptr;
	
	function __construct($fn) {
		if (($this->fptr = fopen($fn, "r")) === FALSE)
			throw new Exception("error: can't open input file " . $fn);
		print("opened " . $fn . "<br>");
		$this->db = new SRDb();
	}
		
	function translate() {		
		$seq = 1;

		for ($ln = 0; (($toks = fgetcsv($this->fptr)) !== FALSE) && $ln < 150; $ln++) {

			print("*** " + $toks[0] . "<br>");

			if (strpos($toks[0], "SRP") === 0) {	
			
				print_r($toks);
				print("<br>");
			
				if (strpos($toks[0], "1") == 3) 
					$type = "Segment";
				else if (strpos($toks[0], "6") == 3) 
					$type = "General";
				else
					$type = "Unknown";
					
				// get file name, use procnum.txt if file name not specified
				$procFn = $toks[2];
				if (empty($procFn))
					$procFn = $toks[0] . ".txt";
				
				try {
					$pid = $this->db->addProc($toks[0], $seq, $type, $toks[1], $procFn);
				}
				catch (Exception $e) {
					print $e->getMessage() . "<br>\n";
				}	
				$seq++;
			}
		}			
		print "Done " . $seq;
	}
}

if ($_GET['fn']) 
	$fn = $_GET['fn'];
else {
	print("error: no filename");
	return;
}

try {
	$trans = new ProcOutlineTrans($fn);
}
catch(Exception $e) {
	print "error: " + $e->getMessage();
	return;
}
try {
	$trans->translate();
}
catch(Exception $e) {
	print "error: " + $e->getMessage();
	return;
}
?>