<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "boocas";
$port = 3308; // add this line

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
