<?php
$group = isset($_GET['group']) ? $_GET['group'] : null;
$kojiKampId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if (!$kojiKampId) {
  echo json_encode(['error' => 'Invalid input']);
  exit;
}

if (!$group || !$kojiKampId) {
    die('Missing required parameters.');
}

// Sanitize the 'group' parameter to prevent unwanted SQL usage
$database = 'ca_' . preg_replace('/[^a-zA-Z0-9_]/', '', $group);

require "config_cikat.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the SQL statement with a placeholder for kampId
    $sql = "SELECT * FROM periodi WHERE aktivan = 1 AND deleted = 0 AND kampId = :kampId";
    $stmt = $db->prepare($sql);

    // Bind the kampId parameter to prevent SQL injection
    $stmt->bindParam(':kampId', $kojiKampId, PDO::PARAM_INT);

    // Execute the prepared statement
    $stmt->execute();

    $colors2 = [];
    $gjoni = [];
    $images = [];
    $pero = [];

    // Loop through rows to build feature arrays
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    //$gjoni["uid"] = $row['uid'];
    $gjoni["tip"] = $row['tip'];
    $gjoni["minDana"] = $row['minDanRez'];  
    $gjoni["datumOd"] = $row['datumOd'];
    $gjoni["datumDo"] = $row['datumDo'];
    $gjoni["ponD"] = $row['ponDolazak'];
    $gjoni["utoD"] = $row['utoDolazak'];
    $gjoni["sriD"] = $row['sriDolazak'];
    $gjoni["cetD"] = $row['cetDolazak'];
    $gjoni["petD"] = $row['petDolazak'];
    $gjoni["subD"] = $row['subDolazak'];
    $gjoni["nedD"] = $row['nedDolazak'];
    $gjoni["ponO"] = $row['ponOdlazak'];
    $gjoni["utoO"] = $row['utoOdlazak'];
    $gjoni["sriO"] = $row['sriOdlazak'];
    $gjoni["cetO"] = $row['cetOdlazak'];
    $gjoni["petO"] = $row['petOdlazak'];
    $gjoni["subO"] = $row['subOdlazak'];
    $gjoni["nedO"] = $row['nedOdlazak'];    

    array_push($pero, $gjoni);

}

echo json_encode($pero);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
