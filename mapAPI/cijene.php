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

  $sql_brand = "SELECT DISTINCT naziv, oznakaPhobs FROM vrstaSJ WHERE deleted = 0 AND kampId = '" . $kojiKampId . "' ";
  $sql_cijene = "SELECT datum, rateFrom FROM cjenik WHERE unitId = :brandname AND kampId = '" . $kojiKampId . "'";

# Try query or error
$dbquery_brand = $db->query($sql_brand);
if (!$dbquery_brand) {
    echo 'An SQL error occured.\n';
    exit;
}

$colors2 = [];
$gjoni = [];

$cjenik = [];

# Loop through rows to build feature arrays
while ($row = $dbquery_brand->fetch(PDO::FETCH_ASSOC)) {

    $brand = $row['oznakaPhobs'];
    //echo $brand;
    $temp = [];

    $nazivBranda = $row['naziv'];

    $cjenik[$row['naziv']] = $temp;


    $sql_cijene = $db->prepare('SELECT DATE_FORMAT(datum, "%d.%m.%Y.") AS datum, rateFrom FROM cjenik WHERE unitId = :brandname');
    $sql_cijene->bindParam('brandname', $brand, PDO::PARAM_STR);
    $test = $sql_cijene->execute();

    while ($row = $sql_cijene->fetch(PDO::FETCH_ASSOC)) {

        $cijena[date] = $row['datum'];
        $cijena[price] = ($row['rateFrom'] <> 0) ? $row['rateFrom'] . '€' : '-' . '€';

        array_push($cjenik[$nazivBranda], $cijena);

    }

}

echo json_encode($cjenik, JSON_UNESCAPED_UNICODE);

header('Content-type: application/json');

$conn = NULL;
?>
