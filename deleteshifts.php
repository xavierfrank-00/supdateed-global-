<?php
include 'apiMain.php';
// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Check if EMPID is provided in the query string
    if (isset($_GET['EMPID'])) {
        $empid = $conn->real_escape_string($_GET['EMPID']);

        // Prepare the DELETE statement
        $sql = "DELETE FROM TBL_SHIFT WHERE EMPID = '$empid'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("status" => "success", "message" => "Record deleted successfully."));
        } else {
            echo json_encode(array("error" => "Error deleting record: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "EMPID parameter is missing."));
    }
}

// Close connection
$conn->close();
?>
