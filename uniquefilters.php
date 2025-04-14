<?php
include 'apiMain.php';
function getColumnNames($conn, $table) {
    $sql = "SHOW COLUMNS FROM $table";
    $queryResult = $conn->query($sql);
    $columns = [];
    
    if ($queryResult) {
        while ($row = $queryResult->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }
    
    return $columns;
}

function getUniqueValues($conn, $table, $columns) {
    $result = [];
    
    foreach ($columns as $column) {
        $sql = "SELECT DISTINCT $column FROM $table";
        $queryResult = $conn->query($sql);
        
        if ($queryResult) {
            $values = [];
            while ($row = $queryResult->fetch_assoc()) {
                $values[] = $row[$column];
            }
            $result[$column] = $values;
        } else {
            $result[$column] = ['error' => "Query failed for column: $column"];
        }
    }

    return $result;
}

// Define the columns you want to get unique values from
$columnsToFetch = [
    'EMPID', 'EMPNAME', 'ROLE', 'DEPARTMENT', 'TEAM', 'PROJECT', 'SHIFT'
];

// Specify your table name
$table = 'EMP_DB'; 

// Get all column names from the table
$allColumns = getColumnNames($conn, $table);

// Get unique values for the specified columns
$uniqueValues = getUniqueValues($conn, $table, $columnsToFetch);

// Prepare response data
$response = [
    'columns' => $allColumns,
    'data' => $uniqueValues
];

// Return as JSON
echo json_encode($response);

// Close connection
$conn->close();
?>
