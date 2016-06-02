<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../css/srdb.css">
</head>
<body id="grad6">
<?php
$ncr = $_GET["ncr"];
if (!$ncr) $ncr = $_POST['ncr'];
if (!$ncr)
	echo "Need NCR #";
else
{
	echo "<p><a href='../../pages/ncr.php?ncr=$ncr'><b>Return to NCR #$ncr</a>";
	createUploadForm($ncr);
	echo "<p><b>Attaching file to NCR #$ncr</b><p>";
	if ($_FILES['filename']) doUpload($ncr);
}
?>
</body>
</html>

<?php
function createUploadForm($ncr)
{
	echo "<p>";
	echo "<table border='5' cellpadding='4' cellspacing='0' style='background:#fff8dc'>";
	echo "<form method='post' action='./attachment.php' enctype='multipart/form-data'>";
	echo "<input type='hidden' name='ncr' value='$ncr'>";
	echo "<tr>";
	echo "<th align='left'>File to attach</th>";
	echo "<td><input type='file' name='filename' size='50'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='2' align='right'><input type='submit' value='Attach File To NCR'></td>";
	echo "</tr>";
	echo "</form>";
	echo "</table><p>";
}
function doUpload($ncr)
{
	$attachDir = "/webFiles/www/public/optics/segrepair/db/content/ncr"; ///h/obsdata/ncr";
	//$attachDir = "/webFiles/www/public/software/ndlog/attach/ncr"; ///h/obsdata/ncr";
	$MAX = 5000000;
	$name = $_FILES['filename']['name'];
	echo "File name = $name<br>";
	if (!$name)
	{
		error("No file selected");
		return;
	}
	$type = $_FILES['filename']['type'];
	echo "File type = $type<br>";
	$size = $_FILES['filename']['size'];
	echo "File size = $size<br>";
	if (!checkType($type)) return;
	if (!checkError($_FILES['filename']['error'])) return;
	if ($_FILES['filename']['size'] > $MAX)
	{
		error("File is too big");
		return;
	}
	$file = $_FILES['filename']['tmp_name'];
	if (!$file || strlen($file) == 0) return;
	$data = fread(fopen($file, "r"), $size);
	if (!$data) return;
	$outfile = "$attachDir/$ncr/$name";
	if (file_exists($outfile))
	{
		error("File already exists");
		return;
	}
	if (!saveFile($outfile, $name, $data, $size)) return;
	echo "File uploaded";
}
function checkType($type)
{
	switch ($type)
	{
		case "text/plain":
		case "image/gif":
		case "image/jpeg":
		case "image/png":
		case "application/msword":
		case "application/pdf":
		case "application/vnd.ms-excel":
		case "application/vnd.ms-powerpoint":
		case "application/vnd.ms-visio.viewer":
		case "application/vnd.openxmlformats-officedocument.presentationml.presentation":
		case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
		case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
		case "application/octet-stream": // FITS File
			return 1;
		default:
			error("File type not supported");
//			print_r($_FILES);
//			print_r($_FILES[filename]);
			return 0;
	}
	return 0;
}
function checkError($error)
{
	switch ($error)
	{
		case 1:
		case 2:
			error("File is too big");
			return 0;
		case 3:
			error("File is incomplete");
			return 0;
		case 4:
			return 0;
		case 0:
		default:
	}
	return 1;
}
function checkName($name)
{
	if (file_exists($name))
	{
		error("File already exists");
		return 0;
	}
	return 1;
}
function error($error)
{
	echo "<font color='#FF0000'>$error</font>";
}
function saveFile($outfile, $name, $data, $size)
{
	$dir = str_replace($name, "", $outfile);
	echo "$dir<br>";
	if (!is_dir($dir))
		if (!mkdir($dir, 0777, TRUE))
		{
			error("Can't create directory to save file");
			return 0;
		}
	$fp = fopen($outfile, "w");
	if (!$fp)
	{
		error("Can't open file for writing");
		return 0;
	}
	fwrite($fp, $data, $size);
	fclose($fp);
	return 1;
}
?>
