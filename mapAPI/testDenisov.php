<?php
$database   = 'ca_' . $_GET['group'];
require "config_oikos.php";

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

  $sql = "SELECT naziv, color, lineColor FROM vrstaSJ WHERE deleted = 0 AND kampId = '" . $kojiKampId . "' AND coalesce(color, '') <> '' ";


# Try query or error
$dbquery = $db->query($sql);
if (!$dbquery) {
    echo 'An SQL error occured.\n';
    exit;
}

// var brandColors = {
//   "Premium": [{
//     "color": "#f2c48d",
//     "border": "#b39169"
//   }],
//   "Family": [{
//     "color": "#f28586",
//     "border": "#c26b6c"
//   }],
//   "Deluxe": [{
//     "color": "#add8e6",
//     "border": "#31708f"
//   }]
// };

$colors2 = [];
$gjoni = [];

# Loop through rows to build feature arrays
while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)) {
    $pero = [];

    $colors2["color"] = $row['color'];
    $colors2["border"] = $row['lineColor'];

    array_push($pero, $colors2);
    $gjoni[$row['naziv']] = $pero;

}

echo json_encode($gjoni);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
