<?php
//--------------------------------------------------------------
//
// getIpDescription.php
//
// Returns the description for the given inspection plan number
//
//--------------------------------------------------------------
include_once "database.inc";
$db = dbConnect();

$ip = $_GET['ip'];

$query = "select * from ip_description where ip='$ip' order by last_update desc limit 1";

$ret = returnArray($query);

dbClose($db);
echo json_encode($ret);
?>
