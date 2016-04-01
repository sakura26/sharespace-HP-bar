<?php
// this one is counting history for average data
include 'config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$conn->query(" SET NAMES UTF8;");
$sql = "SELECT 'lastmonth', sum(donate_value) as total_donate_lastmonth, avg(donate_value) as avg_donate_lastmonth, sum(visitors) as total_visitors_lastmonth, avg(visitors) as avg_visitors_lastmonth FROM history WHERE input_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, '%Y/%m/01' ) and input_date < DATE_FORMAT( CURRENT_DATE, '%Y/%m/01' ) and type='keyholder' and barid=2".
    " UNION ".
    "SELECT 'thismonth', sum(donate_value) as total_donate_lastmonth, avg(donate_value) as avg_donate_lastmonth, sum(visitors) as total_visitors_lastmonth, avg(visitors) as avg_visitors_lastmonth FROM history WHERE input_date >= DATE_FORMAT( CURRENT_DATE, '%Y/%m/01' ) and type='keyholder' and barid=2;";
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
?>
