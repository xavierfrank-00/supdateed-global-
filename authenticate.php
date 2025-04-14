<?php
include 'apiMain.php';
session_start();

// Simulated user credentials (Replace with database validation)
$valid_username = "admin";
$valid_password = "12345";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username == $valid_username && $password == $valid_password) {
        $_SESSION["username"] = $username;
        $_SESSION["LAST_ACTIVITY"] = time(); // Store login time
        $_SESSION["EXPIRE_TIME"] = 15; // Session expires in 60 seconds

        header("Location: login.php"); // Redirect to dashboard
        exit();
    } else {
        echo "Invalid username or password!";
    }
}
?>
