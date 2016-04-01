<?php
//https://moztw-space.rmstudio.tw/metrics.php
//{"month":"2015 \/ 09","events":"12","visitors":"109"}
//https://moztw-space.rmstudio.tw/donate.php
//1315
include 'config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo '{ "status":"error", "desc":"db not accessable"}';
    return;
    //die("Connection failed: " . $conn->connect_error);
} 

$conn->query(" SET NAMES UTF8;");

$json = file_get_contents('https://moztw-space.rmstudio.tw/metrics.php');
$obj = json_decode($json);
$dt = explode ( '/' , $obj->month );

$sql = "SELECT * FROM history WHERE barid=2 AND type='keyholder' AND user='script' AND YEAR(input_date)=? AND MONTH(input_date)=?;";  
echo "SELECT * FROM history WHERE barid=2 AND type='keyholder' AND user='script' AND YEAR(input_date)=".trim($dt[0])." AND MONTH(input_date)=".trim($dt[1]).";";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", trim($dt[0]), trim($dt[1])); 
$stmt->execute();
if ($stmt->num_rows==0){   
	echo ":".$stmt->num_rows.":";
	$stmt->close();
	//do insert
	$donate = file_get_contents('https://moztw-space.rmstudio.tw/donate.php');
	$sql2 = "INSERT INTO history(barid, input_date, user, type, donate_value, visitors) VALUES(2, ?, 'script', 'keyholder', ?, ?);";
	$stmt2 = $conn->prepare($sql2);
	$dateee = trim($dt[0])."-".trim($dt[1])."-1";
	$visitors = $obj->visitors;
	//echo $dateee.", ". $donate.", ".$visitors." WTF:".mysqli_error($conn);
	syslog ( LOG_NOTICE , "moztw: fetch keyholder data ".$dateee.", donate:". $donate.", visitors:".$visitors );
	$stmt2->bind_param("sii", $dateee, $donate, $visitors); 
	$stmt2->execute();
	if ($stmt2->affected_rows==1)
		echo '{ "status":"success" }';  
	else
		echo '{ "status":"error", "debug":"'.mysqli_error($conn).'" }';
}
else{
	echo '{ "status":"error", "desc":"already inserted" }';
}

$conn->close();
?>
