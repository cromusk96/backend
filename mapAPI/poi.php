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

  $sql = "SELECT * FROM tockeInteresa where deleted = 0 AND vrstaPoi = '1' AND kampId = '" . $kojiKampId . "' ";


# Try query or error
$dbquery = $db->query($sql);
if (!$dbquery) {
    echo 'An SQL error occured.\n';
    exit;
}

$colors2 = [];
$gjoni = [];
$images = [];
$rezultat = [];

//samo za demo loadera
//sleep(2);

# Loop through rows to build feature arrays
while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)) {
    $pero = [];

    $gjoni["mapaId"] = $row['mapaId'];
    $gjoni["naziv"] = $row['naziv'];
    $gjoni["ikonica"] = $row['ikonica'];
    $gjoni["latitude"] = $row['latitude'];
    $gjoni["longitude"] = $row['longitude'];
    $gjoni["aktivna"] = $row['aktivna'];
    $gjoni["noclick"] = $row['noclick'];
    $gjoni["straniKljuc"] = $row['straniKljuc'];

    $gjoni["www"] = $row['www'];
    $gjoni["wwwTekst"] = $row['wwwText'];
    $gjoni["opis"] = $row['opis'];
    $gjoni["slika"] = $row['slika1'];

    array_push($rezultat, $gjoni);

}

echo json_encode($rezultat);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
