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

  $sql = "select b.uid, b.mapaId, v.tip, v.oznakaMISH, v.oznakaPHOBS, v.naziv, v.propertyPhobs, v.pmsId, b.broj, b.brojMISH, b.brojGps, b.pmsUnitId, b.dostupna, b.duzina, b.sirina, b.povrsina, b.samoNaUpit FROM vrstaSJ v INNER JOIN brojSJ b ON v.uid = b.vrstaSJ AND v.kampId = b.kampId AND b.deleted = 0 AND v.kampId = '" . $kojiKampId . "' ";

if (isset($_GET['brojsj'])) {
  $brojsj = $_GET['brojsj'];
  $sql = "select b.uid, b.mapaId, v.tip, v.oznakaMISH, v.oznakaPHOBS, v.naziv, b.broj, v.pmsId, b.brojMISH, b.brojGps, b.pmsUnitId, b.dostupna, b.duzina, b.sirina, b.povrsina, b.samoNaUpit FROM vrstaSJ v INNER JOIN brojSJ b ON v.uid = b.vrstaSJ AND v.kampId = b.kampId AND b.deleted = 0 AND v.kampId = '" . $kojiKampId . "' and b.brojMISH = '$brojsj' ";
}


# Try query or error
$dbquery = $db->query($sql);
if (!$dbquery) {
    echo 'An SQL error occured.\n';
    exit;
}

$parcela = [];
$gjoni = [];
$pero = [];

# Loop through rows to build feature arrays
while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)) {
  //$pero = [];

  $parcela["uid"] = $row['uid'];
  $parcela["canbook"] = '';
  $parcela["tip"] = $row['tip'];
  $parcela["tipNaziv"] = $row['naziv'];
  $parcela["mapaId"] = $row['mapaId'];
  $parcela["oznakaMISH"] = $row['oznakaMISH'];
  $parcela["brojGps"] = $row['brojGps'];
  $parcela["pmsUnitId"] = $row['pmsUnitId'];
  $parcela["oznakaPHOBS"] = $row['oznakaPHOBS'];
  $parcela["propertyPhobs"] = $row['propertyPhobs'];
  $parcela["pmsPropertyId"] = isset($row['pmsId']) ? $row['pmsId'] : '';
  $parcela["broj"] = $row['broj'];
  $parcela["brojMISH"] = $row['brojMISH'];
  $parcela["dostupna"] = $row['dostupna'];
  $parcela["samoNaUpit"] = $row['samoNaUpit'];
  $parcela["duzina"] = $row['duzina'];
  $parcela["sirina"] = $row['sirina'];
  $parcela["povrsina"] = $row['povrsina'];
  //$parcela["osuncanost"] = $row['osuncanost'];

  array_push($pero, $parcela);
  $gjoni[] = $pero;


}

echo json_encode($pero, JSON_UNESCAPED_UNICODE);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
