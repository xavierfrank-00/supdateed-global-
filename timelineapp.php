<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

try {
    // Database connection parameters
    $host = "pmsglobal.cve060ce6je3.us-east-1.rds.amazonaws.com";
    $dbname = "PMS_PRO";
    $username = "admin";
    $password = "wfxicVdxG71bJvdVhFN2";

    // Define the DSN (Data Source Name)
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

    // Create a PDO instance
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve data from the POST request
    $inputData = file_get_contents('php://input');
    parse_str($inputData, $parsedData);

    // Set filter parameters from incoming request, default to 'ALL' if not set
    $date = isset($parsedData['date']) ? $parsedData['date'] : date('Y-m-d');
    $empid = isset($parsedData['EMPID']) ? $parsedData['EMPID'] : 'ALL';
    $userId = isset($parsedData['userid']) ? $parsedData['userid'] : 'ALL';


    // Prepare the SQL statement
    $stmt = $pdo->prepare("CALL PR_APP_URL_USAGE_TIMELINE (:date, :empid, :userId)");
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':empid', $empid);
    $stmt->bindParam(':userId', $userId);


    // Execute the statement
    $stmt->execute();

    // Fetch the data
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output the results in JSON format
    echo json_encode($results, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    // Output error in JSON format
    echo json_encode(["error" => $e->getMessage()]);
}

// Close the connection
$pdo = null;
?>
