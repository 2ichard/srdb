<?php
//--------------------------------------------------------------
//
// getComponentDescription.php
//
// Returns the component description for the given PN
//
//--------------------------------------------------------------
$pn = $_GET['pn'];

$desc = array("771-C5110"=>"Axial Insert Assembly",
		"771-C5111"=>"Axial Insert Part A",
		"771-C5112"=>"Axial Insert Part B",
		"771-C5201"=>"Radial Pad",
		"771-22A814"=>"Flex Rod (old)",
		"771-C22A814"=>"Flex Rod (new)",
		"771-C22A814A"=>"Flex Rod (re-plated)",
		"SRP124"=>"Schott Etch",
		"Other"=>"See is_condition");

echo $desc[$pn];

//--------------------------------------------------------------
//
// getComponentLot.php
//
// Returns the component lot for the given PN
//
//--------------------------------------------------------------
/*
include_once "database.inc";
$db = dbConnect();

$pn = $_GET['pn'];
if (preg_match("/22A814/", $pn))
	$pn = "flex_rods";
else if (preg_match("/C5110/", $pn))
	$pn = "axial_inserts";
else if (preg_match("/C5201/", $pn))
	$pn = "radial_pads";
else if (preg_match("/SRP124/", $pn))
	$pn = "etch_soln";
else
	$pn = "nothing";
$query = "select * from lots where type like '$pn%' and status='Active'";
$result = returnArray($query);
if (!$result["lot"]) $result["lot"] = "";
echo $result["lot"];

dbClose($db);
*/
?>
