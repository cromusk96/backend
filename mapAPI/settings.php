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

  $sql = "SELECT * FROM kampovi WHERE uid = '" . $kojiKampId . "' ";


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
    $pero = [];

    $gjoni["naziv"] = $row['naziv'];
    $gjoni["logo"] = $row['logo'];
    $gjoni["otvorenOd"] = $row['otvorenOd'];
    $gjoni["otvorenDo"] = $row['otvorenDo'];
    $gjoni["minDanRez"] = $row['minDanRez'];
    $gjoni["minDanRezMobilke"] = $row['minDanRezMobilke'];

    $gjoni["panomIframe"] = $row['panomiframe'];

    /*$gjoni["maxOdraslihP"] = $row['brojOsoba'];
    $gjoni["maxDjeceP"] = $row['brojDjece'];
    $gjoni["maxOdraslihMH"] = $row['brojOsobaMH'];
    $gjoni["maxDjeceMH"] = $row['brojDjeceMH'];*/

    $gjoni["propertyId"] = $row['propertyid'];
    $gjoni["defaultRateId"] = $row['defaultRateId'];
    $gjoni["parceleRateId"] = $row['rateIdParcele'];
    $gjoni["defaultimg"] = $row['defaultSlika'];
    $gjoni["sortFilterByRb"] = $row['sortFilterRb'];
    $gjoni["cuvanjeRezMin"] = $row['cuvanjeRezMinuta'];
    $gjoni["zatvoriBooking"] = $row['zatvoriBooking'];
    $gjoni["bookingModul"] = $row['bookingModul'];
    $gjoni["popunjavanjeRupa"] = $row['popunjavanjeRupa'];

    $gjoni["langEn"] = $row['en'];
    $gjoni["langDe"] = $row['de'];
    $gjoni["langHr"] = $row['hr'];
    $gjoni["langIt"] = $row['it'];
    $gjoni["langPl"] = $row['pl'];
    $gjoni["langNl"] = $row['nl'];
    $gjoni["langRu"] = $row['ru'];
    $gjoni["langSi"] = $row['si'];

    $gjoni["brojOsoba"] = $row['brojOsoba'];
    $gjoni["brojDjece"] = $row['brojDjece'];
    $gjoni["brojOsobaMh"] = $row['brojOsobaMh'];
    $gjoni["brojDjeceMh"] = $row['brojDjeceMh'];

    $gjoni["maintenanceMode"] = $row['maintenanceMode'];
    $gjoni["prikaziUdaljenosti"] = $row['showDistances'];
    $gjoni["navigacija"] = $row['navigacija'];

    $gjoni["kampId"] = $row['uid'];
    $gjoni["tabletBearerToken"] = $row['tabletBearerToken'];

}

echo json_encode($gjoni);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
