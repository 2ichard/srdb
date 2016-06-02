<?php
//--------------------------------------------------------------
//
// getSegSN.php
//
// Returns a json_encoded array of segment s/n for the given
// segment number
//
//--------------------------------------------------------------
include_once "database.inc";
$db = dbConnect();

$seg = $_GET['seg'];

$query = "select * from segs where type=$seg";

$result = returnMultiArray($query);

$num = 0;
foreach ($result as $sn)
{
	$ret[$num]["name"] = $seg."-".$sn["sn"];
	//$ret[$num]["value"] = $sn["sn"];
	$ret[$num]["value"] = $ret[$num]["name"];
	$ret[$num]["id"] = $sn["sn"];
	$num++;
}

dbClose($db);
echo json_encode($ret);
?>
