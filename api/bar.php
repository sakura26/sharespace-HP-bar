<?php
include 'config.php';
include 'healthlib.php';

// Create connection
/*
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$conn->query(" SET NAMES UTF8;");
$sql = "SELECT * FROM bar";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {  //TODO: get parameter
        echo json_encode($row, JSON_UNESCAPED_UNICODE);
        break;
    }
} else {
    echo '{ "status":"error"}';
}
$conn->close();*/
echo json_encode(readHealth(getHealthData(2)), JSON_UNESCAPED_UNICODE);

/*
array_walk_recursive($data, function(&$value, $key) {
    if(is_string($value)) {
        $value = urlencode($value);
    }
});
$json = urldecode(json_encode($data));


$sth = mysqli_query("SELECT ...");
$rows = array();
while($r = mysqli_fetch_assoc($sth)) {
    $rows[] = $r;
}
print json_encode($rows);
echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";*/
?>
