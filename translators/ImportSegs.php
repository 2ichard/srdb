<?php
require ("../lib/SRDb.php");

/*
To make the csv file properly readable, I had to %s/^V^M/\r/g
*/

class ImportSegs {

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

			
			if ($toks[0] >= 1 && $toks[0] <= 6) {
				print("*** " + $toks[0] . "<br>");
				$this->db->addSeg(0, $toks[0], $toks[1]);
			}
		}
	}
}

if ($_GET['fn']) 
	$fn = $_GET['fn'];
else {
	print("error: no filename");
	return;
}

try {
	$trans = new ImportSegs($fn);
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