<?php
$database = 'ca_' . filter_var($_GET['group'], FILTER_SANITIZE_STRING);
require "config_cikat.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$kojiKampId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if (!$kojiKampId) {
  echo json_encode(['error' => 'Invalid input']);
  exit;
}

$DATE_FROM = filter_var($_GET['dateFrom'], FILTER_SANITIZE_STRING);
$DATE_TO = filter_var($_GET['dateTo'], FILTER_SANITIZE_STRING);

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the main query using placeholders
    $sql = "SELECT * FROM zatvorenerez 
            WHERE kampId = :kampId 
            AND aktivna = '1' 
            AND deleted = '0' 
            AND (:dateFrom BETWEEN STR_TO_DATE(datumOd,'%Y-%m-%d') AND STR_TO_DATE(datumDo,'%Y-%m-%d') 
            OR :dateTo BETWEEN STR_TO_DATE(datumOd,'%Y-%m-%d') AND STR_TO_DATE(datumDo,'%Y-%m-%d'))";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':kampId', $kojiKampId, PDO::PARAM_INT);
    $stmt->bindParam(':dateFrom', $DATE_FROM, PDO::PARAM_STR);
    $stmt->bindParam(':dateTo', $DATE_TO, PDO::PARAM_STR);
    $stmt->execute();

    $zatvoreneRez = [];

    // Loop through rows to build feature arrays
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tempBrojevi = [];

        // Prepare and execute subquery if 'vrsta' is 'V'
        if ($row['vrsta'] == 'V') {
            $sqlBrojevi = "SELECT brojMISH FROM brojSJ WHERE kampId = :kampId AND vrstaMISH = :vrstaMISH";
            $stmtBrojevi = $db->prepare($sqlBrojevi);
            $stmtBrojevi->bindParam(':kampId', $kojiKampId, PDO::PARAM_INT);
            $stmtBrojevi->bindParam(':vrstaMISH', $row['oznaka'], PDO::PARAM_STR);
            $stmtBrojevi->execute();

            while ($rowBrojevi = $stmtBrojevi->fetch(PDO::FETCH_ASSOC)) {
                $tempBrojevi[] = $rowBrojevi["brojMISH"];
            }
        } else {
            $tempBrojevi[] = $row['oznaka'];
        }

        $gjoni = [
            "vrsta" => $row['vrsta'],
            "oznaka" => $tempBrojevi,
            "datumOd" => $row['datumOd'],
            "datumDo" => $row['datumDo']
        ];

        $zatvoreneRez[$row['uid']] = $gjoni;
    }

    header('Content-type: application/json');
    echo json_encode($zatvoreneRez);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>
