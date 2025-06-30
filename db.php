<?php
$host = "sql307.infinityfree.com";
$dbname = "if0_39203974_easystudy";
$username = "if0_39203974";
$password = "CD67rvMYDo";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
