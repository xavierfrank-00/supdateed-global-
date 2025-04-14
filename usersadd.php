<?php
include 'apiMain.php';
// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Check if JSON decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        exit;
    }

    // Retrieve POST data with default values if not set
    $empid = $data['empid'] ?? '';
    $empname = $data['empname'] ?? '';
    $email = $data['email'] ?? '';
    $sysUserName = $data['sysUserName'] ?? '';
    $role = $data['role'] ?? '';
    $designationcategory = $data['designationcategory'] ?? '';
    $reporting1 = $data['reporting1'] ?? '';
    $reporting2 = $data['reporting2'] ?? '';
    $department = $data['department'] ?? '';
    $team = $data['team'] ?? '';
    $project = $data['project'] ?? '';
    $shift = $data['shift'] ?? '';
    $allotedBreak = $data['allotedBreak'] ?? '';
    $requiredproductivehrs = $data['requiredproductivehrs'] ?? '';
    $activeYn = isset($data['activeYn']) ? ($data['activeYn'] === 'Y' ? 'Y' : 'N') : 'N'; // Store as 'Y' or 'N'
    $holidayCountry = $data['holidayCountry'] ?? '';
    $region = $data['region'] ?? '';
    $updatedBy = $data['updatedBy'] ?? '';
    $password = $data['password'] ?? '';
    $accessRole = $data['accessRole'] ?? '';

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare(
        "INSERT INTO EMP_DB (EMPID, EMPNAME, EMAIL, SYS_USER_NAME, ROLE, DESIGNATION_CATEGORY, REPORTING_1, REPORTING_2, DEPARTMENT, TEAM, PROJECT, SHIFT, ALLOTED_BREAK, REQUIRED_PRODUCTIVE_HRS, ACTIVE_YN, HOLIDAY_COUNTRY, REGION, UPDATED_BY, PASSWORD, ACCESS_ROLE, CREATE_DT, UPDATED_DT) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
    );

    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare SQL statement']);
        exit;
    }

    // Bind parameters
    $stmt->bind_param(
        "ssssssssssssssssssss", 
        $empid, $empname, $email, $sysUserName, $role, $designationcategory, $reporting1, 
        $reporting2, $department, $team, $project, $shift, $allotedBreak, $requiredproductivehrs, 
        $activeYn, $holidayCountry, $region, $updatedBy, $password, $accessRole
    );

    // Execute statement and return appropriate response
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

// Close the connection
$conn->close();
?>
