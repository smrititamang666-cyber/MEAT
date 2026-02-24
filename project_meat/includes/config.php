<?php
// Start session for authentication across pages
if(!session_id()) {
    session_start();
}

// Database credentials
$host = "localhost";          // Usually localhost
$db_name = "meat";  // Database name we created
$username = "root";           // MySQL username
$password = "";               // MySQL password

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}//else
//echo 'database connected suceess';  

// Set character set to UTF-8 for Nepali support
$conn->set_charset("utf8");

?>