<?php
//--------------------------------------------------------------
//
// getAllIPs.php
//
// Returns a json_encoded array of allinspection plans
//
//--------------------------------------------------------------
include_once "database.inc";

$db = dbConnect();

$query = "select * from ip_description order by ip,last_update desc";
$values = returnMultiArray($query);
$num = 0;
$ipArray = array();
$ret = array();
foreach ($values as $array)
{
	if (!in_array($array["ip"], $ipArray))
	{
		array_push($ipArray, $array["ip"]);
		$ret[$num] = $array;
		$num++;
	}
}

dbClose($db);

echo json_encode($ret);
?>
