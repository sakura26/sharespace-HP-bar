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
$sql = "UPDATE bar SET name=?,avatar_uri=?,value_current=?,value_max=?,rate=?,cost_avg=?,cost_interval=?,visitor_avg=?,unit=?,last_update=NOW() WHERE barid=2;";  
$stmt = $conn->prepare($sql);
//bar_name=null&bar_avatar=&bar_vol=15687.38117284&bar_max=40000&bar_max_unit=60&bar_rate=0.0015&bar_avg_cost=20000&bar_cost_interval=2592000&bar_avg_visit=200
$stmt->bind_param("ssiiddids", $_GET["bar_name"],$_GET["bar_avatar"],$_GET["bar_vol"],$_GET["bar_max"],$_GET["bar_rate"],$_GET["bar_avg_cost"],$_GET["bar_cost_interval"],$_GET["bar_avg_visit"],$_GET["bar_unit"]); 
$stmt->execute();
if ($stmt->affected_rows==1){   
	echo '{ "status":"success" }';  
}
else
	echo '{ "status":"error", "debug":"'.mysqli_error($conn).'" }';

$conn->close();
?>
