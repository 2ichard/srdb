<?php
//--------------------------------------------------------------
//
// getAllCAs.php
//
// Returns a json_encoded array of all corrective actions
//
//--------------------------------------------------------------
include_once "database.inc";

$type = $_GET["type"];
if (!$type) $type = "all";

$db = dbConnect();

$today = date("Y-m-d");

$query = "select * from ncr_action ";
switch ($type)
{
	case "all":
		break;
	case "closed":
		$query .= "where close_who is not null and close_who!='' ";
		break;
	case "open":
		$query .= "where close_who is null or close_who='' ";
		break;
	case "search":
		$str = $_GET["str"];
		$query .= "where action like '%$str%' ";
		break;
	default:
}

$query .= "order by id desc";

$ret = returnMultiArray($query);

foreach ($ret as $key=>$array)
{
	$ret[$key]["due_date"] = substr($ret[$key]["due_date"], 0, 10);
	$ret[$key]["status"] = "open";
	if ($ret[$key]["close_who"]) $ret[$key]["status"] = "closed";
}

dbClose($db);

echo json_encode($ret);
?>
