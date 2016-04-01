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
$sql = "INSERT INTO user(nickname, email, pass, permission) VALUES (?,?,md5(?),?);";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $_GET["useradd_nickname"],$_GET["useradd_email"],$_GET["useradd_pass"],$_GET["useradd_permission"]); 
$stmt->execute(); //TODO error
//echo '{ "status":"success", "debug":"'.mysqli_error($conn).'" }';
echo '{ "status":"success" }';

$conn->close();
?>

