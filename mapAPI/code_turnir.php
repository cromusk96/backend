<?php
$database   = 'ca_' . $_GET['group'];
require "config_cikat.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-type: application/json');

$code = $_GET['code'];
$db = new PDO($dsn, $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Fetch the record based on the code
    $sql = "SELECT * FROM kodovi WHERE code = :code";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':code', $code, PDO::PARAM_STR);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        echo json_encode(['error' => 'Invalid code.']);
        exit;
    }

    if ($record['seen'] == 1) {
        // If the record was already seen, do not return the point column
        unset($record['point']);
        $response = $record;
    } else {
        // If the record is seen for the first time, update the seen and seentime columns
        $sqlUpdate = "UPDATE kodovi SET seen = 1, seentime = NOW() WHERE code = :code";
        $stmtUpdate = $db->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':code', $code, PDO::PARAM_STR);
        $stmtUpdate->execute();

        $response = $record;
    }

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['error' => 'An SQL error occurred: ' . $e->getMessage()]);
    exit;
}

$conn = NULL;
?>
