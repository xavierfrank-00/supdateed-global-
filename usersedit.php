<?php
include 'apiMain.php';
// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Check if JSON decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        exit;
    }

    // Extract the rest of the employee data
    $empid = $data['EMPID'];
    $empname = $data['EMPNAME'];
    $email = $data['EMAIL'];
    $sysUserName = $data['SYS_USER_NAME'];
    $role = $data['ROLE'];
    $designationcategory = $data['DESIGNATION_CATEGORY'] ?? '';
    $reporting2 = $data['REPORTING_2'];
    $reporting1 = $data['REPORTING_1'];
    $reporting2 = $data['REPORTING_2'];
    $department = $data['DEPARTMENT'];
    $team = $data['TEAM'];
    $project = $data['PROJECT'];
    $shift = $data['SHIFT'];
    $allotedBreak = $data['ALLOTED_BREAK'];
    $requiredproductivehrs = $data['REQUIRED_PRODUCTIVE_HRS'] ?? '';
    $activeYn = $data['ACTIVE_YN'];
    $holidayCountry = $data['HOLIDAY_COUNTRY'];
    $region = $data['REGION'];
    $updatedBy = $data['UPDATED_BY'];

    // Prepare SQL statement for updating employee data
    $stmt = $conn->prepare("UPDATE EMP_DB SET EMPNAME=?, EMAIL=?, SYS_USER_NAME=?, ROLE=?, DESIGNATION_CATEGORY=?, REPORTING_1=?, REPORTING_2=?, DEPARTMENT=?, TEAM=?, PROJECT=?, SHIFT=?, ALLOTED_BREAK=?, REQUIRED_PRODUCTIVE_HRS=?, ACTIVE_YN=?, HOLIDAY_COUNTRY=?, REGION=?, UPDATED_BY=? WHERE EMPID=?");
    $stmt->bind_param("ssssssssssssssssss", $empname, $email, $sysUserName, $role,$designationcategory, $reporting1, $reporting2, $department, $team, $project, $shift, $allotedBreak,$requiredproductivehrs, $activeYn, $holidayCountry, $region, $updatedBy, $empid);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Employee data updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

// Close the connection
$conn->close();
?>
