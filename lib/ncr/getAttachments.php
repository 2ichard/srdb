<?php
//--------------------------------------------------------------
//
// getAttachments.php
//
//--------------------------------------------------------------

$ncr = $_GET["ncr"];
if (!$ncr)
	$output["error"] = "Please supply an NCR #";
else
{
	$cmd = "ls /webFiles/www/public/optics/segrepair/db/content/ncr/$ncr/*";
	//$cmd = "ls /webFiles/www/public/software/ndlog/attach/ncr/$ncr/*";
	$list = explode("\n", shell_exec($cmd));
	$num = 0;
	foreach ($list as $name)
		if ($name)
		{
			$url = str_replace("/webFiles/www/public", "http://www.keck.hawaii.edu/", $name);
			$output["files"][$num]["loc"] = $url;
			$split = explode("/", $name);
			$cnt = count($split) - 1;
			$output["files"][$num]["name"] = $split[$cnt];
			$num++;
		}
}

echo json_encode($output);
?>
