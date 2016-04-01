<?php
include 'config.php';

session_start();
if ($_SESSION["userid"]!=null && $_SESSION["user_json"]!=null) { //already login
    echo $_SESSION["user_json"];
    return;
}

//TODO: clear parameters
//$_GET['email']
//$_GET['passwd']

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->query(" SET NAMES UTF8;");


$stmt = $conn->prepare("SELECT userid, nickname, email, permission FROM user WHERE email=? AND pass=?;");
$stmt->bind_param("ss", $_GET['login_mail'], md5($_GET['login_passwd']));
$stmt->execute();
$result = $stmt->get_result();
if ($myrow = $result->fetch_assoc()) {
    $_SESSION["user_json"] = json_encode($myrow, JSON_UNESCAPED_UNICODE);
    $_SESSION["userid"] = $myrow['userid'];
    echo $_SESSION["user_json"];
}else {
    echo '{ "status":"error"}';
}

$conn->close();

?>
