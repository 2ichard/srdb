<?php
//--------------------------------------------------------------
//
// addLimitedProcess.php
//
// Submits a new limited process record to seg.ncr_limitedprocess.
//
//--------------------------------------------------------------
include_once "database.inc";
$db = dbConnect();
//--------------------------------------------------------------
// Input fields
//--------------------------------------------------------------
$fields = array("ncr",
		"limited_process",
		"username");
//--------------------------------------------------------------
// Input values
//--------------------------------------------------------------
foreach ($fields as $f)
	${$f} = $_GET[$f];
//--------------------------------------------------------------
// Verify values
//--------------------------------------------------------------
$ret["error"] = "";
if (!$ncr)
	$ret["error"] = "Unknown NCR number";
else if (!$limited_process)
	$ret["error"] = "Please enter text for the limited process";
if (!$ret["error"])
{
	//------------------------------------------------------
	// Construct the database query
	//------------------------------------------------------
	$num = 0;
	$query = "insert into ncr_limitedprocess set ";
	foreach ($fields as $f)
	{
		$num++;
		$query .= "$f='${$f}'";
		if ($num < count($fields))
			$query .= ",";
	}
	$ret["query"] = $query;
	//------------------------------------------------------
	// Submit query
	//------------------------------------------------------
	if (!sendQuery($query))
		$ret["error"] = "Error submitting query\n\n$query";
}
dbClose($db);
echo json_encode($ret);
?>
