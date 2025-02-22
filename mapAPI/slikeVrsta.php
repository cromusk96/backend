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

  $sql = "SELECT * FROM vrstaSJ WHERE kampId = '" . $kojiKampId . "' ";


# Try query or error
$dbquery = $db->query($sql);
if (!$dbquery) {
    echo 'An SQL error occured.\n';
    exit;
}

$colors2 = [];
$gjoni = [];
$images = [];

$pero = [];

//samo za demo loadera
//sleep(0.5);

# Loop through rows to build feature arrays
while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)) {

    if ($row['slika1'] <> '') array_push($images, $row['slika1']);
    if ($row['slika2'] <> '') array_push($images, $row['slika2']);
    if ($row['slika3'] <> '') array_push($images, $row['slika3']);
    if ($row['slika4'] <> '') array_push($images, $row['slika4']);
    if ($row['slika5'] <> '') array_push($images, $row['slika5']);
    if ($row['slika6'] <> '') array_push($images, $row['slika6']);
    if ($row['slika7'] <> '') array_push($images, $row['slika7']);
    if ($row['slika8'] <> '') array_push($images, $row['slika8']);

    $gjoni["vrstaSJ"] = $row['uid'];
    $gjoni["naziv"] = $row['naziv'];
    $gjoni["panom"] = $row['slika'];
    $gjoni["nofilter"] = $row['nofilter'];
    $gjoni["images"] = $images;

    array_push($pero, $gjoni);

    $images = [];

}

echo json_encode($pero);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
