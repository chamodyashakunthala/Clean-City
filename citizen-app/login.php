<?php
session_start();
include "db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        
        $_SESSION['user'] = $user['full_name'];

        echo "Login Successful";
        // redirect example
        // header("Location: dashboard.php");

    } else {
        echo "Wrong Password";
    }

} else {
    echo "User Not Found";
}
?>