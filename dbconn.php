<?php
// Database connection details
$servername = "localhost";  // XAMPP typically uses localhost as the server name
$username = "root";  // Default username for phpMyAdmin on XAMPP
$password = "";  // Default password for phpMyAdmin on XAMPP (usually empty)
$dbname = "miniapp";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
?>

