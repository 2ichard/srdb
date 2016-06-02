<?php
//--------------------------------------------------------------
//
// getIP.php
//
// Returns a json_encoded array with the desired inspection plan
//
//--------------------------------------------------------------
$ip = $_GET["ip"];
if (!$ip)
	$ret = "IP # not supplied";
else
{
	include_once "database.inc";

	$db = dbConnect();

	$query = "select * from ip_description where ip='$ip' order by last_update desc limit 1";
	$ret = returnArray($query);
	if (count($ret) == 0)
		$ret = "IP not found";

	dbClose($db);
}

echo json_encode($ret);
?>
