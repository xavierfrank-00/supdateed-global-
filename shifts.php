<?php
include 'apiMain.php';
// Function to extract columns from the result set
function extractColumns($result) {
    $columns = [];
    $meta = $result->fetch_fields();
    foreach ($meta as $field) {
        $columns[] = $field->name;
    }
    return $columns;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Extract parameters from the input data
    $empid = isset($data['empId']) ? $data['empId'] : 'ALL';
    $empname = isset($data['empName']) ? $data['empName'] : 'ALL';
    $startDate = isset($data['startDate']) ? $data['startDate'] : date('Y-m-d');
    $endDate = isset($data['endDate']) ? $data['endDate'] : date('Y-m-d');
    $roles = isset($data['roles']) ? $data['roles'] : 'ALL';
    $project = isset($data['project']) ? $data['project'] : 'ALL';
    $team = isset($data['team']) ? $data['team'] : 'ALL';
    $dept = isset($data['dept']) ? $data['dept'] : 'ALL';
    $userid = isset($data['userid']) ? $data['userid'] : 'ALL'; // Add userid

    

    $empid = $conn->real_escape_string($empid);
    $empname = $conn->real_escape_string($empname);
    $startDate = $conn->real_escape_string($startDate);
    $endDate = $conn->real_escape_string($endDate);
    $roles = $conn->real_escape_string($roles);
    $project = $conn->real_escape_string($project);
    $team = $conn->real_escape_string($team);
    $dept = $conn->real_escape_string($dept);
    $userid = $conn->real_escape_string($userid); // Escape userid


    // Build the query to call the stored procedure
    $proc_call = "CALL PR_TBL_SHIFT('$startDate', '$endDate', '$empid', '$empname', '$dept', '$roles', '$project', '$team', '$userid')";


    // Execute the stored procedure
    if ($result = $conn->query($proc_call)) {
        $data = [];
        $columns = extractColumns($result);

        // Fetch rows
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(array(
            "columns" => $columns,
            "data" => $data
        ));
      
    } else {
        echo json_encode(array("error" => $conn->error));
    }
} 

    


// Handle PUT request
else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Extract parameters from the input data
    if (
        isset($data['EMPID']) && isset($data['EMPNAME']) &&
        isset($data['SYS_USER_NAME']) && isset($data['SHIFTTYPE']) &&
        isset($data['SHIFT_START_TIME']) && isset($data['SHIFT_END_TIME']) &&
        isset($data['SHIFTSTART_DT']) && isset($data['SHIFTEND_DT']) &&
        isset($data['TIME_ZONE']) && isset($data['WEEKOFF']) &&
        isset($data['COMMENTS'])
    ) {
        $EMPID = $conn->real_escape_string($data['EMPID']);
        $EMPNAME = $conn->real_escape_string($data['EMPNAME']);
        $SYS_USER_NAME = $conn->real_escape_string($data['SYS_USER_NAME']);
        $SHIFTTYPE = $conn->real_escape_string($data['SHIFTTYPE']);
        $SHIFT_START_TIME = $conn->real_escape_string($data['SHIFT_START_TIME']);
        $SHIFT_END_TIME = $conn->real_escape_string($data['SHIFT_END_TIME']);
        $SHIFTSTART_DT = $conn->real_escape_string($data['SHIFTSTART_DT']);
        $SHIFTEND_DT = $conn->real_escape_string($data['SHIFTEND_DT']);
        $TIME_ZONE = $conn->real_escape_string($data['TIME_ZONE']);
        $WEEKOFF = $conn->real_escape_string($data['WEEKOFF']);
        $COMMENTS = $conn->real_escape_string($data['COMMENTS']);

       

        // Call the stored procedure
        $sql = "CALL PR_SHIFT_UPDATE('$EMPID', '$EMPNAME', '$SYS_USER_NAME', '$SHIFTTYPE', '$SHIFT_START_TIME', '$SHIFT_END_TIME', '$SHIFTSTART_DT', '$SHIFTEND_DT', '$TIME_ZONE', '$WEEKOFF', '$COMMENTS', 'PREM')";


        if ($conn->query($sql)) {
            echo json_encode(array("status" => "success", "message" => "Data updated successfully."));
        } else {
            echo json_encode(array("error" => $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Incomplete data."));
    }
}

// Close connection
$conn->close();
?>
