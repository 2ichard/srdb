<?php
//--------------------------------------------------------------
//
// getAllNcr.php
//
// Returns a json_encoded array of NCR entries from the database.
//
//--------------------------------------------------------------
include_once "database.inc";

$db = dbConnect();

$seg_type = $_GET["seg_type"];
list(,$seg_sn) = explode("-", $_GET["seg_sn"]);
$status = $_GET["stat"];
//echo "$seg_type<br>$seg_sn<br>$status<br>";

$query = "select * from compliance_report";
if ($seg_type || $seg_sn || $status)
{
	$query .= " where ";
	$num = 0;
	foreach (array("seg_type", "seg_sn", "status") as $val)
	{
		if (${$val})
		{
			if ($num) $query .= " and ";
			if ($val == "status")
			{
				if ($status == "closed") $query .= "date_end is not null";
				else if ($status == "open") $query .= "date_end is null";
			}
			else $query .= "$val='${$val}'";
			$num++;
		}
	}
}
$query .= " order by last_update desc";
//echo "$query<br>";
$values = returnMultiArray($query);
//echo count($values)."<br>";
$num = 0;
$ncrArray = array();
$ret = array();
foreach ($values as $array)
{
	if (!in_array($array["ncr"], $ncrArray))
	{
		array_push($ncrArray, $array["ncr"]);
		// Add - back to seg_sn
		if ($array["seg_sn"]) $array["seg_sn"] = $array["seg_type"]."-".$array["seg_sn"];
		// Is this NCR open/closed?
		$array["status"] = "open";
		if ($array["date_end"]) $array["status"] = "closed";
		// Limited process
		$query = "select * from ncr_limitedprocess where ncr=$array[ncr] order by last_update desc limit 1";
		$lp = returnMultiArray($query);
		$array["limited_process"] = "";
		$array["lpusername"] = "";
		foreach ($lp as $key=>$l)
		{
			if ($key > 0) {
				$array["limited_process"] .= "\n";
				$array["lpusername"] .= "/";
			}
			$array["limited_process"] .= $l["limited_process"];
			$array["lpusername"] .= $l["username"];
		}
		// Approvals
		$query = "select * from ncr_approval where ncr=$array[ncr]";
		$ap = returnMultiArray($query);
		$array["engineering_ap"] = "";
		$array["quality_ap"] = "";
		$array["rework_ap"] = "";
		foreach ($ap as $a)
		{
			if ($a["type"] == "Engineering") $array["engineering_ap"] = $a["username"];
			if ($a["type"] == "Quality") $array["quality_ap"] = $a["username"];
			if ($a["type"] == "Rework") $array["rework_ap"] = $a["username"];
		}
		// Shorter versions
		$dot = "";
		if (strlen($array["is_condition"]) > 50) $dot = "...";
		$array["is_condition_small"] = substr($array["is_condition"], 0, 50).$dot;
		$dot = "";
		if (strlen($array["disposition"]) > 50) $dot = "...";
		$array["disposition_small"] = substr($array["disposition"], 0, 50).$dot;
		$dot = "";
		if (strlen($array["limited_process"]) > 50) $dot = "...";
		$array["limited_process_small"] = substr($array["limited_process"], 0, 50).$dot;
		// Add to list
		$ret[$num] = $array;
		$num++;
	}
}

dbClose($db);

echo json_encode($ret, JSON_NUMERIC_CHECK);
?>
