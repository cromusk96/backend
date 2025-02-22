<?php
$group = filter_input(INPUT_GET, 'group', FILTER_SANITIZE_STRING);
$mapaIds = filter_input(INPUT_GET, 'mapaids', FILTER_SANITIZE_STRING);
$kojiKampId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$group || !$mapaIds || !$kojiKampId) {
    die(json_encode(['error' => 'Missing required parameters']));
}

$database = 'ca_' . $group;
require "config_cikat.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-type: application/json');

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $gjoni = [];
    $images = [];

    if ($mapaIds === '*') {
        // Query when all mapaIds are required
        $sql = "SELECT o.naziv, o.noclick, o.mapaId, vO.naziv AS vrsta, o.recommended 
                FROM objekti o
                JOIN vrsteObjekata vO ON vO.uid = o.vrstaObjektaUid 
                WHERE o.deleted = 0 
                AND o.kampId = :kampId";

        $sql2 = "SELECT bSj.mapaId, bSj.openModal, bSj.slika, bSj.noclick, bSj.brojMish, bSj.brojGps, vSJ.oznakaPhobs, vSJ.oznaka, vSJ.slika AS vrstaSJ_slika, vSJ.tip, vSJ.naziv, vSJ.color, bSj.broj, vSJ.oznakaMish, bSj.dostupna, bSj.samoNaUpit
                FROM brojSJ bSj
                JOIN vrstaSJ vSJ on bSj.vrstaSJ = vSJ.uid
                WHERE bSj.deleted = 0 
                AND bSj.kampId = :kampId";

        $stmt = $db->prepare($sql);
        $stmt2 = $db->prepare($sql2);

        $stmt->bindParam(':kampId', $kojiKampId, PDO::PARAM_INT);
        $stmt2->bindParam(':kampId', $kojiKampId, PDO::PARAM_INT);

        $stmt->execute();
        $stmt2->execute();
    } else {
        // Split and sanitize mapaIds
        $mapaIdsArray = array_map('intval', explode(',', $mapaIds));

        if (empty($mapaIdsArray)) {
            die(json_encode(['error' => 'No valid mapaIds provided']));
        }

        // Create placeholders for mapaIds
        $placeholders = implode(',', array_fill(0, count($mapaIdsArray), '?'));

        $sql = "SELECT o.naziv, o.mapaId, vO.naziv AS vrsta, o.recommended 
                FROM objekti o
                JOIN vrsteObjekata vO ON vO.uid = o.vrstaObjektaUid 
                WHERE o.mapaId IN ($placeholders) 
                AND o.deleted = 0 
                AND o.kampId = ?";

        $sql2 = "SELECT bSj.mapaId, bSj.slika, bSj.brojMish, bSj.brojGps, vSJ.oznaka, vSJ.slika AS vrstaSJ_slika, vSJ.tip, vSJ.naziv, vSJ.color, bSj.broj, vSJ.oznakaMish, bSj.dostupna, bSj.samoNaUpit
                FROM brojSJ bSj
                JOIN vrstaSJ vSJ on bSj.vrstaSJ = vSJ.uid
                WHERE bSj.mapaId IN ($placeholders) 
                AND bSj.deleted = 0 
                AND bSj.kampId = ?";

        $stmt = $db->prepare($sql);
        $stmt2 = $db->prepare($sql2);

        // Merge mapaIdsArray with the kampId for parameter binding
        $stmt->execute(array_merge($mapaIdsArray, [$kojiKampId]));
        $stmt2->execute(array_merge($mapaIdsArray, [$kojiKampId]));
    }
    // Fetch results from the first query (objects)
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $vrsta = $row['vrsta'];
            
        $entry = [
            "mapId" => $row['mapaId'],
            "name" => $row['naziv'],
            "category" => $row['vrsta'],
            "recommended" => $row['recommended'] !== "0",
            "featureType" => "object",
            "noClick" => $row['noclick'] !== "0"
        ];

        if ($vrsta == 'Recepcija') {
            $entry["icon"] = "fa-circle-info";
        } else if ($vrsta == 'Restoran') {
            $entry["icon"] = "fa-utensils";
        } else if ($vrsta == 'Sanitarija') {
            $entry["icon"] = "fa-restroom";
        } else if ($vrsta == 'Masaža') {
            $entry["icon"] = "fa-spa";
        } else {
            $entry["icon"] = "";
        }
        
        $gjoni[] = $entry;
    }

    // Fetch results from the second query (units)
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $category = (stripos($row['naziv'], 'Glamping') !== false) ? 'G' : $row['tip'];
    
        $entry = [
            "mapId" => $row['mapaId'],
            "pmsNumber" => $row['brojMish'], //TODO rename once database is rebuilt
            "gpsNumber" => $row['brojGps'],
            "label" => $row['oznaka'],
            "labelMish" => $row['oznakaMish'],
            "labelPhobs" => $row['oznakaPhobs'],
            "enabled" => $row['dostupna'],
            "onQuery" => $row['samoNaUpit'],
            "has360" => $row['slika'] ? true : $row["vrstaSJ_slika"] ? true : false,
            "name" => $row['naziv'],
            "category" => $category,
            "featureType" => "unit",
            "color" => $row['color'],
            "number" => $row['broj'],
            "noClick" => $row['noclick'] !== "0",
            "openModal" => $row['openModal'] !== "0"
        ];
    
        if ($category == 'G') {
            $entry["icon"] = "fa-tent";
        } else if ($category == 'M') {
            $entry["icon"] = "fa-house";
        } else if ($category == 'P') {
            $entry["icon"] = "fa-caravan";
        }else {
            $entry["icon"] = "";
        }
    
        $gjoni[] = $entry;
    }

    // Sort the final array by mapId
    /*usort($gjoni, function ($a, $b) {
        if (!isset($a['mapId']) || !isset($b['mapId'])) {
            throw new Exception('Missing mapId in one or more entries, unable to sort.');
        }
        return $a['mapId'] <=> $b['mapId'];
    });*/

    // Return the result as JSON
    echo json_encode($gjoni);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$db = null;
?>
