<?php
include 'apiMain.php';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepare and execute the stored procedure
    $stmt = $pdo->prepare("CALL PR_TBL_EMP_DB('ALL', 'ALL','ALL','ALL','ALL','ALL','ALL','ALL')");
    $stmt->execute();

    // Fetch the data
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get column names
    $columnNames = array_keys($result[0]);
    
    // Create an associative array for columns and data
    $response = [
        'columns' => $columnNames,
        'data' => $result
    ];

    // Set content type to JSON and output the response
    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    // Handle database connection errors
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
