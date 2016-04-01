<?php
include 'config.php';

session_start();
if ($_SESSION["userid"]!=null && $_SESSION["user_json"]!=null) { //get permission
    $permission=json_decode($_SESSION["user_json"])->permission;
}
if ($permission!="admin"){
    echo '{ "status":"error", "desc":"no permission"}';
    return;
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$conn->query(" SET NAMES UTF8;");
$sql = "SELECT userid, nickname, email, permission FROM user;";  //TODO: get parameter
$result = $conn->query($sql);
$rows = array();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    	$rows[] = $row;
    }
    echo json_encode($rows);
} else {
    echo '{ "status":"error"}';
}
$conn->close();
?>
