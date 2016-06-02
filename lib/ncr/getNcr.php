<?php
//--------------------------------------------------------------
//
// getNcr.php
//
// Returns a json_encoded array for the given NCR number
//
//--------------------------------------------------------------
include_once "database.inc";

$db = dbConnect();

$values = array();
$values["error"] = "";

if ($_GET["ncr"])
{
	$query = "select * from compliance_report where ncr=".$_GET["ncr"]." order by last_update desc limit 1";
	$values = returnArray($query);
	if (!$values["ncr"])
		$values["error"] = "NCR not found";
}
else
	$values["error"] = "No NCR number supplied";

dbClose($db);

echo json_encode($values);
?>
