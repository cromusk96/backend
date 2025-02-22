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

  $sql = "SELECT * FROM vrstaSJ WHERE deleted = 0 AND kampId = '" . $kojiKampId . "' ";


# Try query or error
$dbquery = $db->query($sql);
if (!$dbquery) {
    echo 'An SQL error occured.\n';
    exit;
}

$colors2 = [];
$gjoni = [];

# Loop through rows to build feature arrays
while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)) {
    $pero = [];

    $colors2["uid"] = $row['uid'];
    $colors2["oznaka"] = $row['oznaka'];
    $colors2["oznakaPhobs"] = $row['oznakaPhobs'];
    $colors2["tip"] = $row['tip'];
    $colors2["naziv"] = $row['naziv'];
    $colors2["brojOsoba"] = $row['brojOsoba'];
    $colors2["brojDjece"] = $row['brojDjece'];
    $colors2["bookTocnogBroja"] = $row['bookTocnogBroja'];
    $colors2["nePrikazujBroj"] = $row['nePrikazujBroj'];
    $colors2["minRupa"] = $row['minRupa'];
    $colors2["maxRupa"] = $row['maxRupa'];

    array_push($pero, $colors2);
    $gjoni[$row['oznakaMish']] = $pero;

}

echo json_encode($gjoni);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
