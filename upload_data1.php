<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'pos') {
    exit(0);
}

// Get JSON input data
$data = file_get_contents("php://input");
$employees = json_decode($data, true);

// Validate input data
if (!is_array($employees) || count($employees) === 0) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid data format"]);
    exit;
}

// Prepare SQL statement
// Prepare SQL statement
$stmt = $conn->prepare("
    INSERT INTO EMP_DB (
        EMPID, EMPNAME, EMAIL, sys_user_name, ROLE, 
        REPORTING_1, REPORTING_2, DEPARTMENT, DESIGNATION_CATEGORY, REQUIRED_PRODUCTIVE_HRS, 
        TEAM, PROJECT, SHIFT, ALLOTED_BREAK, ACTIVE_YN, 
        HOLIDAY_COUNTRY, REGION, UPDATED_BY, PASSWORD, ACCESS_ROLE
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        EMPNAME = VALUES(EMPNAME),
        EMAIL = VALUES(EMAIL),
        sys_user_name = VALUES(sys_user_name),
        ROLE = VALUES(ROLE),
        REPORTING_1 = VALUES(REPORTING_1),
        REPORTING_2 = VALUES(REPORTING_2),
        DEPARTMENT = VALUES(DEPARTMENT),
        DESIGNATION_CATEGORY = VALUES(DESIGNATION_CATEGORY),
        REQUIRED_PRODUCTIVE_HRS = VALUES(REQUIRED_PRODUCTIVE_HRS),
        TEAM = VALUES(TEAM),
        PROJECT = VALUES(PROJECT),
        SHIFT = VALUES(SHIFT),
        ALLOTED_BREAK = VALUES(ALLOTED_BREAK),
        ACTIVE_YN = VALUES(ACTIVE_YN),
        HOLIDAY_COUNTRY = VALUES(HOLIDAY_COUNTRY),
        REGION = VALUES(REGION),
        UPDATED_BY = VALUES(UPDATED_BY),
        PASSWORD = VALUES(PASSWORD),
        ACCESS_ROLE = VALUES(ACCESS_ROLE)
");

// Check if statement preparation was successful
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["message" => "Failed to prepare SQL statement: " . $conn->error]);
    exit;
}

// Bind parameters (updated type string to match 20 placeholders)
$stmt->bind_param(
    "ssssssssssssssssssss",
    $empid, $empname, $email, $sys_user_name, $role,
    $reporting_1, $reporting_2, $department, $des_cat, $req_pro, 
    $team, $project, $shift, $alloted_break, $active_yn, 
    $holiday_country, $region, $updated_by, $password, $access_role
);

// Execute statement for each employee
foreach ($employees as $employee) {
    $empid = $employee['EMPID'] ?? '';
    $empname = $employee['EMPNAME'] ?? '';
    $email = $employee['EMAIL'] ?? '';
    $sys_user_name = $employee['Sys_user_name'] ?? '';
    $role = $employee['ROLE'] ?? '';
    $reporting_1 = $employee['REPORTING_1'] ?? '';
    $reporting_2 = $employee['REPORTING_2'] ?? '';
    $department = $employee['DEPARTMENT'] ?? '';
    $des_cat = $employee['DESIGNATION_CATEGORY'] ?? '';
    $req_pro = $employee['REQUIRED_PRODUCTIVE_HRS'] ?? '';
    $team = $employee['TEAM'] ?? '';
    $project = $employee['PROJECT'] ?? '';
    $shift = $employee['SHIFT'] ?? '';
    $alloted_break = $employee['ALLOTED_BREAK'] ?? '';
    $active_yn = $employee['ACTIVE_YN'] ?? '';
    $holiday_country = $employee['HOLIDAY_COUNTRY'] ?? '';
    $region = $employee['REGION'] ?? '';
    $updated_by = $employee['UPDATED_BY'] ?? '';
    $password = $employee['PASSWORD'] ?? ''; // Add this line to get PASSWORD
    $access_role = $employee['ACCESS_ROLE'] ?? ''; // Add this line to get ACCESS_ROLE

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["message" => "Error executing query: " . $stmt->error]);
        exit;
    }
}
// Close statement and connection
$stmt->close();
$conn->close();

echo json_encode(["message" => "Data uploaded successfully"]);
?>
