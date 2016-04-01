<?php
include 'config.php';

session_start();
if ($_SESSION["userid"]!=null && $_SESSION["user_json"]!=null) { //get permission
    $permission=json_decode($_SESSION["user_json"])->permission;
}
if ($_GET['mode']=="keyholder" && ($permission=="keyholder" || $permission=="admin"))
	$keyholder=true;
else
	$keyholder=false;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$conn->query(" SET NAMES UTF8;");
if ($keyholder)
	$sql = "SELECT * FROM history WHERE barid=2;";  //TODO: get parameter
else
	$sql = "SELECT * FROM history WHERE type='donate' and barid=2;";  //TODO: get parameter
$result = $conn->query($sql);
$rows = array();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    	$rows[] = $row;
    }
    echo json_encode($rows);
} else {
    //echo '{ "status":"error"}';
}
$conn->close();

/*
$rows = array();
while($r = mysqli_fetch_assoc($sth)) {
    $rows[] = $r;
}
print json_encode($rows);



$sth = mysqli_query("SELECT ...");
$rows = array();
while($r = mysqli_fetch_assoc($sth)) {
    $rows[] = $r;
}
print json_encode($rows);
echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";*/
?>
