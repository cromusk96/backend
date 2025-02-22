<?php
$database   = 'ca_' . $_GET['group'];
require "config_cikat.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$db = new PDO($dsn, $username, $password);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

  $sql = "SELECT t1.*, COALESCE(gt.naziv, 'Other') AS naziv
FROM tockeInteresa AS t1
INNER JOIN (
    SELECT MIN(uid) AS min_uid
    FROM tockeInteresa
    GROUP BY ikonica
) AS t2 ON t1.uid = t2.min_uid
LEFT JOIN grupeTocki AS gt ON t1.grupa = gt.uid;
";


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
    $sqlPrijevodi = "SELECT * FROM prijevodi WHERE deleted = 0 AND text_string = '" . $row['ikonica'] . "' ";
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
    $gjoni[$row['ikonica']] = $pero;
    $gjoni[$row['ikonica']][] = ['group' => $row['naziv']];

}

echo json_encode($gjoni);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
