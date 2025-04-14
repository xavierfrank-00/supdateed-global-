<?php
include 'apiMain.php';
// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$websiteName = $data['websiteName'] ?? null;
$type = $data['type'] ?? null;

if ($websiteName && $type) {
    // Prepare the stored procedure call
    $stmt = $conn->prepare("CALL PR_INSERT_PRODUCTIVE_APPS_WEBSITE (?, ?, ?)");
    
    // Define a fixed string for the type
    $fixedType = "WEBSITE";
    
    // Bind parameters
    $stmt->bind_param("sss", $websiteName, $fixedType, $type);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Record inserted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error executing query: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input."]);
}

// Close connection
$conn->close();
?>
