<?php
//--------------------------------------------------------------
//
// updateNcr.php
//
// Creates or updates an NCR entry in the seg.compliance_report
// database table.
//
//--------------------------------------------------------------
include_once "database.inc";
$db = dbConnect();
$ret["error"] = "";
//--------------------------------------------------------------
// Input fields
//--------------------------------------------------------------
$fields = array("ncr",
		"seg_type",
		"seg_sn",
		"com_pn",
		"com_lot",
		"quantity",
		"is_condition",
		"disposition",
		"cause",
		"keyword",
		"plan_num",
		"date_start",
		"username");
//--------------------------------------------------------------
// Input values
//--------------------------------------------------------------
foreach ($fields as $f)
	${$f} = $_GET[$f];
//--------------------------------------------------------------
// Required fields
//--------------------------------------------------------------
if ((!$seg_type && !$seg_sn) && (!$com_pn && !$com_lot))
	$ret["error"] = "($seg_type,$seg_sn) Please input seg type/sn or com pn/sn";
else if (!$is_condition)
	$ret["error"] = "Please input is condition";
else if ($plan_num == "")
	$ret["error"] = "Please input plan number (0 for none)";
if (!$ret["error"])
{
	$type = "updated";
	//------------------------------------------------------
	// If no NCR #, then get the next one
	//------------------------------------------------------
	if (!$ncr)
	{
		$query = "select ncr from compliance_report order by ncr desc limit 1";
		$result = returnArray($query);
		$ncr = $result["ncr"];
		if (!$ncr)
			$ret["error"] = "Cannot determine next NCR number\n\n$query";
		$ncr += 1;
		$type = "submitted";
	}
	if ($ncr)
	{
		$url = "http://www.keck.hawaii.edu/optics/segrepair/db/pages/ncr/ncr.php?ncr=$ncr";
		$buffer = "NCR #$ncr has been $type.\n\n$url";
		$ret["ncrno"] = $ncr;
		if (!$date_start)
		{
			$date = date("Y-m-d H:i:s");
			$date_start = $date;
		}
		//----------------------------------------------
		// Construct the database query
		//----------------------------------------------
		$query = "insert into compliance_report set ";
		$i = 1;
		foreach ($fields as $f)
		{
			$num++;
			if ($f == "seg_sn")
			{
				$split = explode("-", ${$f});
				$n = count($split) - 1;
				${$f} = $split[$n];
			}
			$query .= "$f='${$f}'";
			if ($i < count($fields)) $query .= ",";
			$i++;
		}
		$ret["query"] = $query;
		//----------------------------------------------
		// Submit query
		//----------------------------------------------
		if (!sendQuery($query))
			$ret["error"] = "Query error\n\n$query";
		else
			// SRP-QA
			mail("lwold@keck.hawaii.edu,jmader@keck.hawaii.edu", "NCR #$ncr $type", $buffer);
	}
}
dbClose($db);
echo json_encode($ret);
?>
