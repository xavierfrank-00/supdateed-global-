<?php
include 'apiMain.php';

// Fetch data from PRODUCTIVE_APPS_WEBSITE table
$sql = "SELECT * FROM PRODUCTIVE_APPS_WEBSITE";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch all column names
    $columns = [];
    while ($fieldinfo = $result->fetch_field()) {
        $columns[] = $fieldinfo->name;
    }

    // Exclude the first three columns
    $columnsToReturn = array_slice($columns, 3);
    
    // Fetch data
    $data = [];
    while ($row = $result->fetch_assoc()) {
        // Filter row to exclude the first three columns
        $filteredRow = array_intersect_key($row, array_flip($columnsToReturn));
        $data[] = $filteredRow;
    }

    // Return response
    echo json_encode([
        "columns" => $columnsToReturn,
        "data" => $data,
    ]);
} else {
    echo json_encode(["message" => "No results found."]);
}

// Close connection
$conn->close();
?>
