<?php
include 'apiMain.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");

session_start();

session_unset();
session_destroy();

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>