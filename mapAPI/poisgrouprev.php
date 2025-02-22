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

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all points of interest for the given camp
    $sql = "
        SELECT 
            ti.*, 
            COALESCE(gt.naziv, 'Other') AS iconGroup
        FROM tockeInteresa AS ti
        LEFT JOIN grupeTocki AS gt ON ti.grupa = gt.uid
        WHERE 
            ti.aktivna = '1' AND 
            ti.vrstaPoi = '1' AND 
            ti.deleted = 0 AND 
            ti.kampId = :kampId
        ORDER BY 
            gt.uid ASC,  -- Orders grupeTocki by uid
            ti.displayIndex ASC  -- Orders tockeInteresa by displayIndex
    ";


    $stmt = $db->prepare($sql);
    $stmt->bindParam(':kampId', $kojiKampId, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $groupedData = [];

    // Process each row
    foreach ($rows as $row) {
        $iconGroup = $row['iconGroup'];
        $ikonica = $row['ikonica'];

        // Initialize group if it doesn't exist
        if (!isset($groupedData[$iconGroup][$ikonica])) {
            $groupedData[$iconGroup][$ikonica] = [
                'ikonica' => $ikonica,
                'resort' => false,
                'camp' => false,
                'mapaId' => $row['mapaId']
            ];
        }

        // Accumulate `resort` and `camp` flags
        $groupedData[$iconGroup][$ikonica]['resort'] = $groupedData[$iconGroup][$ikonica]['resort'] || ($row['resort'] == '1');
        $groupedData[$iconGroup][$ikonica]['camp'] = $groupedData[$iconGroup][$ikonica]['camp'] || ($row['camp'] == '1');
    }

    // Flatten grouped data
    $result = [];
    foreach ($groupedData as $iconGroup => $items) {
        $result[$iconGroup] = array_values($items);
    }

    // Output JSON response
    header('Content-type: application/json');
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

$conn = null;
?>
