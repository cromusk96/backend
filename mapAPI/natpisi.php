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

$sql = "SELECT * FROM natpisi WHERE kampId = '" . $kojiKampId . "' AND active = '1' AND deleted = 0 ";

# Try query or error
$dbquery = $db->query($sql);
if (!$dbquery) {
    echo 'An SQL error occured.\n';
    exit;
}

$geojson = array(
    'type'      => 'FeatureCollection',
    'features'  => array()
 );
 
 # Loop through rows to build feature arrays
 while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)){
     $feature = array(
         'type' => 'Feature',
         'geometry' => array(
             'type' => 'Point',
             # Pass Longitude and Latitude Columns here
             'coordinates' => array($row['latitude'], $row['longitude'])
         ),
         # Pass other attribute columns here
         'properties' => array(
             'id' => $row['mapId'],
             'description' => $row['text'],
             'rotation' => (int) $row['rotation'],
             'fontmin' => (int) $row['fontMin'],
             'fontmax' => (int) $row['fontMax'],
             'color' => $row['color'],
             'pozicija' => $row['text'] == 'SUPPLEMENT MARE' ? 'map' : 'auto',
             'halo' => $row['halo'],
             'halow' => (int) $row['halowidth']
             )
         );
     # Add feature arrays to feature collection array
     array_push($geojson['features'], $feature);
 }
 
 header('Content-type: application/json');
 echo json_encode($geojson);
 $conn = NULL;
 ?>
 