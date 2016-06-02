<?php
//--------------------------------------------------------------
//
// database.inc
//
// Database functions
//
// dbConnect - connect to the seg database
// dbClose - close the database connection
// sendQuery - sends the given query returning 0/1
// returnArray - returns an array of elements (array[key])
// returnMultiArray - returns a multi-d array (array[num][key])
//
//--------------------------------------------------------------
function dbConnect()
{
	$db = mysql_connect("mysqlserver", "segadmin", "2cseg");
	mysql_select_db("seg");
	return $db;
}

function dbClose($db)
{
	mysql_close($db);
}

function sendQuery($query)
{
	if (mysql_query($query)) return 1;
	else return 0;
}

function returnArray($query)
{
	$values = array();
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 1)
	{
		$row = mysql_fetch_assoc($result);
		foreach ($row as $key=>$val)
			if ($val)
				$values[$key] = $val;
	}
	return $values;
}

function returnMultiArray($query)
{
	$values = array();
	$result = mysql_query($query);
	$num = 0;
	while ($row = mysql_fetch_assoc($result))
	{
		foreach ($row as $key=>$val)
			$values[$num][$key] = $val;
		$num++;
	}
	return $values;
}
?>
