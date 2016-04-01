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
    echo '{ "status":"error", "desc":"db not accessable"}';
    return;
    //die("Connection failed: " . $conn->connect_error);
} 

$conn->query(" SET NAMES UTF8;");
$sql = "DELETE FROM user WHERE userid=?;";  
$stmt = $conn->prepare($sql);
$userids = explode ( ',' , $_GET["userid"] );
foreach ($userids as $value) {
    $stmt->bind_param("i", $value); 
    $stmt->execute();
    //if ($stmt->affected_rows==1){     }
}
echo '{ "status":"success" }';

$conn->close();
?>
