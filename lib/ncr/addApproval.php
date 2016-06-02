<?php
//--------------------------------------------------------------
//
// addApproval.php
//
// Submits a new approval record to seg.ncr_approval, if one
// doesn't already exist for the given type.
//
// If the submitted approval is the last for this NCR, then
// update the compliance_report.date_end for this NCR to
// close it out.
//
//--------------------------------------------------------------
include_once "database.inc";
$db = dbConnect();
$ret["error"] = "";
//--------------------------------------------------------------
// Input fields
//--------------------------------------------------------------
$fields = array("ncr",
		"type",
		"username");
//--------------------------------------------------------------
// Input values
//--------------------------------------------------------------
foreach ($fields as $f)
	${$f} = $_GET[$f];
if (!$ncr)
	$ret["error"] = "Unknown NCR number";
else if (!$type)
	$ret["error"] = "Please enter an approval type";
else if (!$username)
	$ret["error"] = "Please enter your email alias";
if (!$ret["error"])
{
	//------------------------------------------------------
	// Verify this approval doesn't exist already
	//------------------------------------------------------
	$query = "select * from ncr_approval where ncr=$ncr and type='$type'";
	$ret["query"] = $query;
	$return = returnArray($query);
	if ($return["id"])
		$ret["error"] = "Approval already exists";
	else
	{
		//----------------------------------------------
		// Construct the database query
		//----------------------------------------------
		$query = "insert into ncr_approval set ";
		$num = 0;
		foreach ($fields as $f)
		{
			$num++;
			$query .= "$f='${$f}'";
			if ($num < count($fields))
				$query .= ",";
		}
		$ret["ncr"] = $ncr;
		//----------------------------------------------
		// Submit query
		//----------------------------------------------
		if (!sendQuery($query))
			$ret["error"] = "Error submitting query\n\n$query";
		else
		{
			//--------------------------------------
			// If all three approvals, end ncr
			//--------------------------------------
			$query = "select * from ncr_approval where ncr=$ncr";
			$return = returnMultiArray($query);
			if (count($return) == 3)
			{
				$date = date("Y-m-d H:i:s", mktime());
				$query = "update compliance_report set date_end='$date' where ncr=$ncr";
				if (!sendQuery($query))
					$ret["error"] = "Error closing NCR\n\n$query";
			}
		}
	}
}
dbClose($db);
echo json_encode($ret);
?>
