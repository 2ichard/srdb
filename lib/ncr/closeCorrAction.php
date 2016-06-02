<?php
//--------------------------------------------------------------
//
// closeCorrAction.php
//
// Closes a corrective action entry
//
//--------------------------------------------------------------
include_once "database.inc";
$db = dbConnect();
//--------------------------------------------------------------
// Input fields
//--------------------------------------------------------------
$id = $_GET["id"];
$username = $_GET["username"];
//--------------------------------------------------------------
// Verify values
//--------------------------------------------------------------
$ret["error"] = "";
if (!$id)
	$ret["error"] = "Unknown ID number";
else if (!$username)
	$ret["error"] = "Please enter your email alias";
if (!$ret["error"])
{
	$date = date("Y-m-d H:i:s");
	//------------------------------------------------------
	// Construct the database query
	//------------------------------------------------------
	$num = 0;
	$query = "update ncr_action set close_who='$username',close_date='$date' where id=$id";
	$ret["query"] = $query;
	//------------------------------------------------------
	// Submit query
	//------------------------------------------------------
	if (!sendQuery($query))
		$ret["error"] = "Error update action ID #$id";
	//------------------------------------------------------
	// Get ncr #
	//------------------------------------------------------
	$query = "select * from ncr_action where id=$id";
	$return = returnArray($query);
	$ret["ncr"] = $return["ncr"];
}
dbClose($db);
echo json_encode($ret);
?>
