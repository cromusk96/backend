<?php
$database   = 'ca_' . preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['group']); // Sanitize 'group' parameter to allow only alphanumeric and underscore
require "config_cikat.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$kojiMapaId = $_GET['mapaid'];
$kojiKampId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if (!$kojiKampId) {
  echo json_encode(['error' => 'Invalid input']);
  exit;
}


    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Using prepared statements to prevent SQL injection
    $sql = "SELECT * FROM objekti WHERE mapaId = :mapaId AND deleted = 0 AND kampId = :kampId";
    $stmt = $db->prepare($sql);

    // Bind parameters to placeholders
    $stmt->bindParam(':mapaId', $kojiMapaId, PDO::PARAM_INT);
    $stmt->bindParam(':kampId', $kojiKampId, PDO::PARAM_INT);

    // Execute the prepared statement
    $stmt->execute();

    $colors2 = [];
    $gjoni = [];
    $images = [];

    // Loop through rows to build feature arrays
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pero = [];

    if ($row['slika1'] <> '') array_push($images, $row['slika1']);
    if ($row['slika2'] <> '') array_push($images, $row['slika2']);
    if ($row['slika3'] <> '') array_push($images, $row['slika3']);
    if ($row['slika4'] <> '') array_push($images, $row['slika4']);
    if ($row['slika5'] <> '') array_push($images, $row['slika5']);
    if ($row['slika6'] <> '') array_push($images, $row['slika6']);
    if ($row['slika7'] <> '') array_push($images, $row['slika7']);
    if ($row['slika8'] <> '') array_push($images, $row['slika8']);

    $gjoni["uid"] = $row['uid'];
    $gjoni["vrstaObjekta"] = $row['vrstaObjektaUid'];
    $gjoni["naziv"] = $row['naziv'];
    $gjoni["podnaziv"] = $row['podnaziv'];
    $gjoni["telefon"] = $row['telefon'];
    $gjoni["mail"] = $row['mail'];
    $gjoni["noteHeader"] = $row['noteHeader'];
    $gjoni["noclick"] = $row['noclick'];
    $gjoni["adresa"] = $row['adresa'];
    $gjoni["radno_vrijeme"] = $row['radno_vrijeme'];
    $gjoni["www"] = $row['www'];
    $gjoni["cjenikurl"] = $row['cjenikUrl'];
    $gjoni["cjenikText"] = $row['cjenikText'];
    $gjoni["urlText"] = $row['urlText'];
    $gjoni["shower"] = $row['shower'];
    $gjoni["sink"] = $row['sink'];
    $gjoni["laundry"] = $row['laundry'];
    $gjoni["childrenToilet"] = $row['childrenToilet'];
    $gjoni["chemicalToilet"] = $row['chemicalToilet'];
    $gjoni["disabledToilet"] = $row['disabledToilet'];    
    $gjoni["privateToilet"] = $row['privateToilet'];   
    $gjoni["clothingWash"] = $row['clothingWash'];   
    $gjoni["dishWash"] = $row['dishWash'];   
    $gjoni["laundry"] = $row['laundry'];   
    $gjoni["dryer"] = $row['dryer'];   
    $gjoni["dogShower"] = $row['dogShower'];   
    $gjoni["refrigerator"] = $row['refrigerator'];   
    $gjoni["ambulanta"] = $row['ambulanta'];   
    $gjoni["restaurant"] = $row['restaurant'];   
    $gjoni["wellness"] = $row['wellness'];   
    $gjoni["hairdresser"] = $row['hairdresser'];   
    $gjoni["fitness"] = $row['fitness'];   
    $gjoni["kiosk"] = $row['kiosk'];   
    $gjoni["bar"] = $row['bar'];  
    $gjoni["napomena_hr"] = $row['napomena_hr'];
    $gjoni["napomena_en"] = $row['napomena_en'];
    $gjoni["napomena_de"] = $row['napomena_de'];
    $gjoni["napomena_it"] = $row['napomena_it'];
    $gjoni["napomena_pl"] = $row['napomena_pl'];
    $gjoni["napomena_nl"] = $row['napomena_nl'];
    $gjoni["napomena_ru"] = $row['napomena_ru'];
    $gjoni["napomena_si"] = $row['napomena_si'];
    $gjoni["panom"] = $row['slika'];
    $gjoni["recommended"] = $row['recommended'];
    $gjoni["images"] = $images;

}

echo json_encode($gjoni);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
