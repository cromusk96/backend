<?php
$database   = 'ca_' . $_GET['group'];
require "config_cikat.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$db = new PDO($dsn, $username, $password);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

  $sql = "SELECT * FROM poilabels";


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

    $sqlPrijevodi = "SELECT * FROM prijevodi WHERE deleted = 0 AND text_string = '" . $row['name'] . "' ";
    $dbqueryPrijevodi = $db->query($sqlPrijevodi);
if (!$dbqueryPrijevodi) {
    echo 'An SQL error occured.\n';
    exit;
}
    while ($rowPrijevodi = $dbqueryPrijevodi->fetch(PDO::FETCH_ASSOC)) {
    $colors2["en"] = $rowPrijevodi["en"];
    $colors2["hr"] = $rowPrijevodi["hr"];
    $colors2["it"] = $rowPrijevodi["it"];
    $colors2["de"] = $rowPrijevodi["de"];
    $colors2["ru"] = $rowPrijevodi["ru"];
    $colors2["si"] = $rowPrijevodi["si"];
    $colors2["pl"] = $rowPrijevodi["pl"];
    $colors2["nl"] = $rowPrijevodi["nl"];

    }
    //$colors2["border"] = $row['border'];

    array_push($pero, $colors2);
    $gjoni[$row['name']] = $pero;

}

echo json_encode($gjoni);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
