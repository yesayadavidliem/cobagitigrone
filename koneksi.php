<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // Adjust with your database username
$password = ""; // Adjust with your database password
$dbname = "opstfsm"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
