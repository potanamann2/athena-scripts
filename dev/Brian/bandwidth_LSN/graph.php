<?php
error_reporting(0); // Turn off all error reporting

$apiKey = 'SET_API_KEY_HERE';
$serverid = 'LSN-D0000';

switch ($_GET['range']) {
	case "hour":
		$start = strtotime("-1 hour");
		break;
	case "day";
		$start = strtotime("-1 day");
		break;
	case "week";
		$start = strtotime("-1 week");
		break;
	default:
		die();
}

$stop = time();

$apiData = file_get_contents("https://rw.limestonenetworks.com/webservices/clientapi.php?key=$apiKey&mod=servers&action=bwgraph&server_id=$serverid&start=$start&stop=$stop");
$len = strlen($apiData);

//Begin writing headers
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");

//Use the switch-generated Content-Type
header("Content-Type: image/gif");
header("Content-Disposition: inline; filename=bwgraph.gif;");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".$len);
echo $apiData;
exit();
?>
