<?php
//--------------------------------------------------------------
//
// updateIP.php
//
// Creates or updates an IP entry in the seg.ip_description
// database table.
//
//--------------------------------------------------------------
include_once "database.inc";
$db = dbConnect();
$ret["error"] = "";
//--------------------------------------------------------------
// Input fields
//--------------------------------------------------------------
$fields = array("ip",
		"description",
		"username");
//--------------------------------------------------------------
// Input values
//--------------------------------------------------------------
foreach ($fields as $f)
	${$f} = $_GET[$f];
//--------------------------------------------------------------
// Required fields
//--------------------------------------------------------------
if (!$ip)
	$ret["error"] = "Please input an IP #";
if (!$description)
	$ret["error"] = "Please input a description";
if (!$ret["error"])
{
	//------------------------------------------------------
	// If no IP #, then get the next one
	//------------------------------------------------------
//	if (!$ip)
//	{
//		$query = "select ip from ip_description order by ip desc limit 1";
//		$result = returnArray($query);
//		$ip = $result["ip"];
//		if (!$ip)
//			$ret["error"] = "Cannot determine next IP number\n\n$query";
//		$ip += 1;
//	}
//	if ($ip)
//	{
		$ret["ip"] = $ip;
		//----------------------------------------------
		// Construct the database query
		//----------------------------------------------
		$query = "insert into ip_description set ";
		$i = 1;
		foreach ($fields as $f)
		{
			$query .= "$f='".addslashes(${$f})."'";
			if ($i < count($fields)) $query .= ",";
			$i++;
		}
		$ret["query"] = $query;
		//----------------------------------------------
		// Submit query
		//----------------------------------------------
		if (!sendQuery($query))
			$ret["error"] = "Query error\n\n$query";
//	}
}
dbClose($db);
echo json_encode($ret);
?>
