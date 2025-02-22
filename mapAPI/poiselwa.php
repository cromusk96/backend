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

$sql = "SELECT ti.*, gt.naziv FROM tockeInteresa AS ti LEFT JOIN grupeTocki AS gt ON ti.grupa = gt.uid WHERE ti.aktivna = '1' AND ti.vrstaPoi <> '1' AND ti.deleted = 0 AND ti.kampId = '" . $kojiKampId . "' ORDER BY ti.redniBroj DESC";


# Try query or error
$dbquery = $db->query($sql);
if (!$dbquery) {
    echo 'An SQL error occured.\n';
    exit;
}

# Build GeoJSON feature collection array
$geojson = array(
    'type'      => 'FeatureCollection',
    'features'  => array()
 );

# Loop through rows to build feature arrays
while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)){

    $klasa = ($row['noclick'] == '1') ? 'interest-point-other noclick' : 'interest-point-other';

    $feature = array(
        'type' => 'Feature',
        'geometry' => array(
            'type' => 'Point',
            # Pass Longitude and Latitude Columns here
            'coordinates' => array(floatval($row['latitude']), floatval($row['longitude']))
        ),
        # Pass other attribute columns here
        'properties' => array(
            'id' => $row['mapaId'],
            'class' => $klasa,
            'nofilter' => ($row['nofilter'] == '1') ? "yes" : "no",
            'navigation' => $row['navigation'],
            'icon' => $row['ikonica'],
            'grupa' => $row['naziv'],
            'www' => $row['www'],
            'wwwTekst' => $row['wwwText'],
            'opis' => $row['opis'],
            'slika' => $row['slika1']
            )
        );
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}

header('Content-type: application/json');
echo json_encode($geojson);
$conn = NULL;
?>
