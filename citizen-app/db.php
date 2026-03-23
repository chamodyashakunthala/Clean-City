<?php
$servername = "localhost";   // usually localhost
$username = "root";          // your MySQL username
$password = "";              // your MySQL password
$dbname = "login_system";    // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
} else {
    echo "Database Connected Successfully!";
}
?>