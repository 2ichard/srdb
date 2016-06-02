<?php
//--------------------------------------------------------------
//
// getLimitedProcess.php
//
// Returns a json_encoded array of limited process records
// from the database.
//
//--------------------------------------------------------------
include_once "database.inc";
$db = dbConnect();
//--------------------------------------------------------------
// Input fields
//--------------------------------------------------------------
$ncr = $_GET["ncr"];
//--------------------------------------------------------------
// Verify values
//--------------------------------------------------------------
$ret["error"] = "";
if (!$ncr)
	$ret["error"] = "Unknown NCR number";
if (!$ret["error"])
{
	//------------------------------------------------------
	// Construct the database query
	//------------------------------------------------------
	$num = 0;
	$query = "select * from ncr_limitedprocess where ncr=$ncr order by last_update desc";
	$ret["query"] = $query;
	//------------------------------------------------------
	// Submit query
	//------------------------------------------------------
	$ret = returnMultiArray($query);
	//------------------------------------------------------
	// Format output
	//------------------------------------------------------
	$process = "";
	$num = count($ret);
	foreach ($ret as $pro)
	{
		if ($process) $process .= "\n";
		$process .= "ID: $num\tName: ".$pro["username"]."\n\n";
		$process .= $pro["limited_process"]."\n";
		$num--;
	}
	$ret["limited_process"] = $process;
}
dbClose($db);
echo json_encode($ret);
?>
