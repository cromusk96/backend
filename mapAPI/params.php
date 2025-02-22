<?php
$database   = 'ca_' . $_GET['group'];
require "config_cikat.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$kojiKampId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if (!$kojiKampId) {
  echo json_encode(['error' => 'Invalid input']);
  exit;
}

$db = new PDO($dsn, $username, $password);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

  $sql = "SELECT * FROM parametri WHERE deleted = 0 AND kampId = '" . $kojiKampId . "' ";


# Try query or error
$dbquery = $db->query($sql);
if (!$dbquery) {
    echo 'An SQL error occured.\n';
    exit;
}

$colors2 = [];
$gjoni = [];
$images = [];

//samo za demo loadera
//sleep(2);

# Loop through rows to build feature arrays
while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)) {

    $gjoni[$row['nazivPolja']] = $row['vrijednostPolja'];

}

echo json_encode($gjoni);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
