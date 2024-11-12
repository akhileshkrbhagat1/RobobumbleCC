<?php
$servername = "sql203.infinityfree.com";
$username = "if0_37686867";
$password = "Yantra123End"; // Set your MySQL password here
$dbname = "if0_37686867_user_access_control";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
