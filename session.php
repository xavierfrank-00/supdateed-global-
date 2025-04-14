<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Database connection settings
$servername = "pmsglobal.cve060ce6je3.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "wfxicVdxG71bJvdVhFN2";
$dbname = "PMS_PRO";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["username"] = $username;
        $_SESSION["LAST_ACTIVITY"] = time();
        $_SESSION["EXPIRE_TIME"] = 100; // 5 minutes (300 seconds)

        echo json_encode(["status" => "success", "message" => "Login successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    }
    $stmt->close();
    $conn->close();
}
?>
?>
