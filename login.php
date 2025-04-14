<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // ✅ Allow frontend
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // ✅ Allow necessary methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // ✅ Allow required headers
header("Access-Control-Allow-Credentials: include"); // ✅ Allow session cookies

session_start();  // ✅ Start session

include 'apiMain.php';  // ✅ Database connection

header("Content-Type: application/json");  // ✅ JSON response

// ✅ Handle CORS preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ✅ Handle POST login request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Read JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['email'], $input['password'])) {
        echo json_encode(["status" => "error", "message" => "Missing email or password"]);
        exit();
    }

    $inputEmail = trim($input['email']);
    $inputPassword = trim($input['password']);

    // ✅ Prepare SQL statement
    $sql = "SELECT * FROM AdminLogin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["status" => "error", "message" => "Database error"]);
        exit();
    }

    $stmt->bind_param("s", $inputEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    // ✅ Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // ✅ Verify password securely
        if (password_verify($inputPassword, $user['password'])) {
            // ✅ Generate secure token
            $loginToken = bin2hex(random_bytes(32)); 

            // ✅ Update token in database
            $updateTokenSql = "UPDATE AdminLogin SET token = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateTokenSql);
            if (!$updateStmt) {
                error_log("Token update failed: " . $conn->error);
                echo json_encode(["status" => "error", "message" => "Token update failed"]);
                exit();
            }

            $updateStmt->bind_param("ss", $loginToken, $inputEmail);
            $updateStmt->execute();
            $updateStmt->close();

            // ✅ Store user session
            $_SESSION['user'] = [
                "email" => $user['email'],
                "name" => $user['name'],
                "role" => $user['role'],
                "token" => $loginToken
            ];

            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "user" => [
                    "email" => $user['email'],
                    "name" => $user['name'],
                    "role" => $user['role']
                ],
                "token" => $loginToken
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>
