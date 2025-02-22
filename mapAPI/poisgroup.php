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

  /*$sql = "SELECT t1.* FROM tockeInteresa AS t1
INNER JOIN (
    SELECT MIN(uid) AS min_uid
    FROM tockeInteresa
    GROUP BY ikonica
) AS t2 ON t1.uid = t2.min_uid";*/

/*$sql = "SELECT t1.*, COALESCE(gt.naziv, 'Other') AS iconGroup
FROM tockeInteresa AS t1
INNER JOIN (
    SELECT MIN(uid) AS min_uid
    FROM tockeInteresa
    GROUP BY ikonica
) AS t2 ON t1.uid = t2.min_uid
LEFT JOIN grupeTocki AS gt ON t1.grupa = gt.uid;
";*/

/*$sql = "SELECT t1.*, COALESCE(gt.naziv, 'Other') AS iconGroup
FROM grupeTocki AS gt
RIGHT JOIN tockeInteresa AS t1 ON gt.uid = t1.grupa
INNER JOIN (
    SELECT MIN(uid) AS min_uid
    FROM tockeInteresa
    GROUP BY ikonica
) AS t2 ON t1.uid = t2.min_uid
ORDER BY gt.naziv ASC;";*/

$sql = "SELECT ti.*, COALESCE(gt.naziv, 'Other') AS iconGroup  
FROM tockeInteresa AS ti
LEFT JOIN grupeTocki AS gt ON ti.grupa = gt.uid
INNER JOIN (
    SELECT MIN(uid) AS min_uid
    FROM tockeInteresa 
    WHERE kampId = '" . $kojiKampId . "'
    AND aktivna = '1'
    AND deleted = 0
    GROUP BY ikonica
) AS t2 ON ti.uid = t2.min_uid
WHERE ti.aktivna = '1' AND ti.vrstaPoi = '1' AND ti.deleted = 0 AND ti.kampId = '" . $kojiKampId . "' 
ORDER BY CASE WHEN gt.naziv IS NULL THEN 1 ELSE 0 END, gt.uid ASC";

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

    $gjoni[$row['iconGroup']][] = $row['ikonica'];

}

echo json_encode($gjoni);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
