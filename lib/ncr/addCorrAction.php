<?php
//--------------------------------------------------------------
//
// addCorrAction.php
//
// Submits a new corrective action record to seg.ncr_action.
// An email is sent to assign_to@keck.hawaii.edu
//
//--------------------------------------------------------------
include_once "database.inc";
$db = dbConnect();
//--------------------------------------------------------------
// Input fields
//--------------------------------------------------------------
$fields = array("ncr",
		"assign_to",
		"due_date",
		"action",
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
	$ret["error"] = "Unknown NCR number -- $ncr";
else if (!$assign_to)
	$ret["error"] = "Please enter an assignee";
else if (!$due_date)
	$ret["error"] = "Please enter a due date";
else if (!$action)
	$ret["error"] = "Please enter a corrective action";
//--------------------------------------------------------------
// Verify due_date
//--------------------------------------------------------------
$err = 0;
$due_date = str_replace("/", "-", $due_date);
list($yr, $mo, $dy) = explode("-", $due_date);
if ($yr >= 2015)
	$due_date = sprintf("%04d-%02d-%02d", $yr, $mo, $dy);
else if ($dy >= 2015 || $dy >= 15)
{
	if ($dy < 1000) $dy += 2000;
	$due_date = sprintf("%04d-%02d-%02d", $dy, $yr, $mo);
}
else
{
	$ret["error"] = "Unknown date format (use yyyy-mm-dd or mm/dd/yy)";
	$err = 1;
}
if (!$err)
{
	list($yr, $mo, $dy) = explode("-", $due_date);
	$dt = mktime(0, 0, 0, $mo, $dy, $yr);
	if ($dt < mktime())
		$ret["error"] = "The due date is in the past";
}
if (!$ret["error"])
{
	//------------------------------------------------------
	// Construct the database query
	//------------------------------------------------------
	$num = 0;
	$query = "insert into ncr_action set ";
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
	else
	{
		//----------------------------------------------
		// Retrieve this id
		//----------------------------------------------
		$query = "select * from ncr_action order by last_update desc limit 1";
		$ret = returnArray($query);
		//----------------------------------------------
		// Send email to assignee
		//----------------------------------------------
		$buffer = "The following non-conformance report has a new corrective action ";
		$buffer .= "that is assigned to you.\n\n";
		$buffer .= "NCR #$ncr\n";
		$buffer .= "Assigned to $assign_to\n";
		$buffer .= "Due date $due_date\n\n";
		$buffer .= "$action\n\n";
		$buffer .= "To view this NCR, visit the following link:\n";
		$buffer .= "https://www.keck.hawaii.edu/optics/segrepair/db/pages/ncr.php?ncr=$ncr";
		mail($assign_to."@keck.hawaii.edu", "NCR corrective action", $buffer);
	}
}
dbClose($db);
echo json_encode($ret);
?>
