<?php
include 'config.php';
include 'healthlib.php';
session_start();
if ($_SESSION["userid"]==null || $_SESSION["user_json"]==null) { 
    echo '{ "status":"error", "desc":"need login"}';
    return;
}
$user_json = json_decode($_SESSION["user_json"]);
if ($user_json->permission!='keyholder' && $user_json->permission!='admin') { 
    echo '{ "status":"error", "desc":"not keyholder", "debug":"'.$user_json->permission.'"}';
    return;
}
//TODO: clear parameters
$keyholder_comment = strip_tags ( $_GET["key_comment"] );
$keyholder_date = $_GET["key_date"];
if ($keyholder_date==null || $keyholder_date=="")
	$keyholder_date = date('Y-m-d H:i:s'); //set to current date
if (preg_match ( '/^\d{4}-\d{1,2}-\d{1,2}(?:\s\d{1,2}:\d{1,2}:\d{1,2})*$/' , $keyholder_date )!=1){
	echo '{ "status":"error", "desc":"bad key_date format", "debug":"'.$keyholder_date.'"}';
	return;
}
$keyholder_user = $user_json->nickname;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->query(" SET NAMES UTF8;");


$stmt = $conn->prepare("INSERT INTO history (barid, input_date, user, type, donate_value, comment, visitors) VALUES (?, ?, ?, 'keyholder', ?, ?, ?);");
$stmt->bind_param("issisi", $lolol=2, $keyholder_date, $keyholder_user, $_GET['key_donates'], $keyholder_comment, $_GET['key_visits']); //key_date, key_visits, key_donates
$stmt->execute();
if ($stmt->affected_rows==1){
	echo '{ "status":"success"}';
	addHealth(2, $_GET['key_donates']);
}
else
	echo '{ "status":"error", "desc":"insert failed"}';

$conn->close();

?>
