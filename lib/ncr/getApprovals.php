<?php
//--------------------------------------------------------------
//
// getApprovals.php
//
// Returns a json_encoded array of approvals for the given NCR.
//
//--------------------------------------------------------------
include_once "database.inc";
$db = dbConnect();
$ret["error"] = "";
//--------------------------------------------------------------
// Input fields
//--------------------------------------------------------------
$ncr = $_GET["ncr"];
if (!$ncr)
	$ret["error"] = "Unknown NCR number";
if (!$ret["error"])
{
	//------------------------------------------------------
	// Retrieve approvals
	//------------------------------------------------------
	$query = "select * from ncr_approval where ncr=$ncr";
	$approvals = returnMultiArray($query);
	foreach ($approvals as $app)
		$ret[$app["type"]] = $app["username"];
	$ret["query"] = $query;
}
dbClose($db);
echo json_encode($ret);
?>
