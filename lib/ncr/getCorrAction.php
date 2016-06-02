<?php
//--------------------------------------------------------------
//
// getCorrAction.php
//
// Returns a json_encoded array of corrective action records
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
	$ret["error"] = "Unknown NCR number -- $ncr --";
if (!$ret["error"])
{
	//------------------------------------------------------
	// Construct the database query
	//------------------------------------------------------
	$num = 0;
	$query = "select * from ncr_action where ncr=$ncr order by due_date desc";
	$ret["query"] = $query;
	//------------------------------------------------------
	// Submit query
	//------------------------------------------------------
	$ret = returnMultiArray($query);
	//------------------------------------------------------
	// Format output
	//------------------------------------------------------
	$action = "";
	$num = count($ret);
	foreach ($ret as $key=>$act)
	{
		$ret[$key]["num"] = $num;
		list($ret[$key]["due_date"]) = explode(" ", $ret[$key]["due_date"]);
		$ret[$key]["status"] = "open";
		if ($ret[$key]["close_who"]) $ret[$key]["status"] = "closed (by ".$ret[$key]["close_who"].")";
		$num--;
	}
//	$ret["corrective_action"] = $action;
}
dbClose($db);
echo json_encode($ret);
?>
