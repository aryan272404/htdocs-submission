<?php
// Define database constants only if not already defined
if (!defined('DB_HOST')) define('DB_HOST', 'sql307.infinityfree.com');
if (!defined('DB_USER')) define('DB_USER', 'if0_39203974');
if (!defined('DB_PASS')) define('DB_PASS', 'CD67rvMYDo');
if (!defined('DB_NAME')) define('DB_NAME', 'if0_39203974_easystudy');

// Connect to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
