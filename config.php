<?php
// Database connection settings
$host = "localhost"; // Change if using a remote database
$dbname = "blog_management"; // Your database name
$username = "root"; // Your database username
$password = ""; // Your database password

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character encoding to prevent issues with special characters
$conn->set_charset("utf8mb4");
?>
