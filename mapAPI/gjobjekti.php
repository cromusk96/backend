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

$db->query("SET NAMES 'utf8'");

  $sql = "SELECT objekti.*, vO.naziv AS nazivVrste FROM objekti JOIN vrsteObjekata vO ON vO.uid = objekti.vrstaObjektaUid WHERE objekti.deleted = 0 AND objekti.kampId = '" . $kojiKampId . "'"; //dodati where kampId = ...
//var_dump($sql);
# Try query or error
$dbquery = $db->query($sql);
if (!$dbquery) {
    echo 'An SQL error occured.\n';
    exit;
}

$parcela = [];
$gjoni = [];
$pero = [];

# Loop through rows to build feature arrays
while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)) {
  //$pero = [];

  $parcela["uid"] = $row['uid'];
  $parcela["vrsta"] = $row['vrstaObjektaUid'];
  $parcela["naziv"] = $row['naziv'];
  $parcela["mapaId"] = $row['mapaId'];
  $parcela["recommended"] = $row['recommended'];
  $parcela["nazivVrste"] = $row['nazivVrste'];
  $parcela["noClick"] = $row['noclick'];
  $parcela["recommended"] = $row['recommended'] == "1" ? true : false;

  array_push($pero, $parcela);
  $gjoni[] = $pero;


}

echo json_encode($pero, JSON_UNESCAPED_UNICODE);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
