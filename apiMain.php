<?php
// db_connection.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Database connection settings
$servername = "pmsglobal.cve060ce6je3.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "wfxicVdxG71bJvdVhFN2";
$dbname = "PMS_PRO";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => "Connection failed: " . $conn->connect_error]));
}
?>
