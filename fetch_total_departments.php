<?php
include 'apiMain.php';
// Get input parameters with default values
$endDate = isset($_GET['dateRange']['end']) ? $_GET['dateRange']['end'] : date('Y-m-d', strtotime('yesterday'));
$startDate = isset($_GET['dateRange']['start']) ? $_GET['dateRange']['start'] : date('Y-m-d', strtotime('yesterday - 6 days'));
$departments = isset($_GET['department']) ? $_GET['department'] : ['ALL'];
$roles = isset($_GET['role']) ? $_GET['role'] : ['ALL'];
$projects = isset($_GET['project']) ? $_GET['project'] : ['ALL'];
$shifts = isset($_GET['shift']) ? $_GET['shift'] : ['ALL'];
$teams = isset($_GET['team']) ? $_GET['team'] : ['ALL'];
$ids = isset($_GET['ids']) ? $_GET['ids'] : ['ALL'];
$names = isset($_GET['names']) ? $_GET['names'] : ['ALL'];

// Convert arrays to comma-separated strings for stored procedure parameters
$departments = implode(",", array_map([$conn, 'real_escape_string'], $departments));
$roles = implode(",", array_map([$conn, 'real_escape_string'], $roles));
$projects = implode(",", array_map([$conn, 'real_escape_string'], $projects));
$shifts = implode(",", array_map([$conn, 'real_escape_string'], $shifts));
$teams = implode(",", array_map([$conn, 'real_escape_string'], $teams));
$ids = implode(",", array_map([$conn, 'real_escape_string'], $ids));
$names = implode(",", array_map([$conn, 'real_escape_string'], $names));

// Initialize arrays for aggregation
$aggregate_data_by_department = [];
$department_row_count = [];

// Prepare and execute the stored procedure
if ($stmt = $conn->prepare("CALL PR_EMPLOYEE_ACTIVITY(?, ?, ?, ?, ?, ?, 'ALL', ?, ?, ?)")) {
    $stmt->bind_param('sssssssss', $startDate, $endDate, $ids, $names, $departments, $roles, $projects, $shifts, $teams);
    $stmt->execute();
    $result_data = $stmt->get_result();

    // Fetch and aggregate data
    while ($row = $result_data->fetch_assoc()) {
        $department = $row['Department'];

        // Initialize department entry if not exists
        if (!isset($aggregate_data_by_department[$department])) {
            $aggregate_data_by_department[$department] = [
                'TotalLoggedHours' => 0,
                'TotalIdleHours' => 0,
                'TotalProductiveHours' => 0,
                'TotalMeetings' => 0,
                'TotalBreaks' => 0,
                'TOTAL_ON_SYSTEM' => 0,
            ];
            $department_row_count[$department] = 0; // Initialize row count
        }

        // Increment row count for the department
        $department_row_count[$department]++;

        // Accumulate the hours (assuming these fields are in 'H:i:s' format)
        $aggregate_data_by_department[$department]['TotalLoggedHours'] += strtotime($row['TotalLoggedHours']) - strtotime('TODAY');
        $aggregate_data_by_department[$department]['TotalIdleHours'] += strtotime($row['TotalIdleHours']) - strtotime('TODAY');
        $aggregate_data_by_department[$department]['TotalProductiveHours'] += strtotime($row['TotalProductiveHours']) - strtotime('TODAY');
        $aggregate_data_by_department[$department]['TotalMeetings'] += strtotime($row['TotalMeetings']) - strtotime('TODAY');
        $aggregate_data_by_department[$department]['TotalBreaks'] += strtotime($row['TotalBreaks']) - strtotime('TODAY');
        $aggregate_data_by_department[$department]['TOTAL_ON_SYSTEM'] += strtotime($row['TOTAL_ON_SYSTEM']) - strtotime('TODAY');
    }

    $stmt->close();
} else {
    die("Failed to prepare statement.");
}

// Calculate the average and format the results with decimal precision
foreach ($aggregate_data_by_department as $department => &$data) {
    // Calculate the average by dividing the accumulated time by the row count
    if ($department_row_count[$department] > 0) {
        $data['TotalLoggedHours'] = round(($data['TotalLoggedHours'] / $department_row_count[$department]) / 3600, 2);  // Convert seconds to hours
        $data['TotalIdleHours'] = round(($data['TotalIdleHours'] / $department_row_count[$department]) / 3600, 2);
        $data['TotalProductiveHours'] = round(($data['TotalProductiveHours'] / $department_row_count[$department]) / 3600, 2);
        $data['TotalMeetings'] = round(($data['TotalMeetings'] / $department_row_count[$department]) / 3600, 2);
        $data['TotalBreaks'] = round(($data['TotalBreaks'] / $department_row_count[$department]) / 3600, 2);
        $data['TOTAL_ON_SYSTEM'] = round(($data['TOTAL_ON_SYSTEM'] / $department_row_count[$department]) / 3600, 2);
    }
}

// Prepare the response
$response = [
    'data' => $aggregate_data_by_department,
];

// Return JSON response
echo json_encode($response);

// Close the connection
$conn->close();
