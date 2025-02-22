<?php
$database   = 'ca_' . $_GET['group'];
require "config_cikat.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$kojiMapaId = filter_var($_GET['mapaid'], FILTER_SANITIZE_STRING); // Sanitize input to prevent invalid characters
$kojiKampId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if (!$kojiKampId || !$kojiMapaId) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get all osuncanost values
    $sql = "SELECT * FROM osuncanost";
    $dbquery = $db->query($sql);
    if (!$dbquery) {
        echo 'An SQL error occured.\n';
        exit;
    }

    $osuncanost = [];
    while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)) {
        $osuncanost[$row['uid']] = $row['osuncanost'];
    }

    // Prepare the query for brojSJ table
    $sql = "SELECT * FROM brojSJ WHERE mapaId = :mapaId AND kampId = :kampId AND deleted = 0";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':mapaId', $kojiMapaId, PDO::PARAM_STR);
    $stmt->bindParam(':kampId', $kojiKampId, PDO::PARAM_INT);
    
    // Execute the query
    $stmt->execute();
    $colors2 = [];
    $gjoni = [];
    $images = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pero = [];

        // Collect images
        for ($i = 1; $i <= 8; $i++) {
            $slikaKey = 'slika' . $i;
            if ($row[$slikaKey] != '') {
                array_push($images, $row[$slikaKey]);
            }
        }

        $gjoni["vrstaSJ"] = $row['vrstaSJ'];
        $gjoni["tipMISH"] = $row['vrstaMish'];
        $gjoni["brojMISH"] = $row['brojMish'];
        $gjoni["brojGps"] = $row['brojGps'];
        $gjoni["pmsUnitId"] = $row['pmsUnitId'];
        $gjoni["broj"] = $row['broj'];
        $gjoni["samoNaUpit"] = $row['samoNaUpit'];
        $gjoni["noclick"] = $row['noclick'];
        $gjoni["noteHeader"] = $row['noteHeader'];
        $gjoni["duzina"] = $row['duzina'];
        $gjoni["sirina"] = $row['sirina'];
        $gjoni["povrsina"] = $row['povrsina'];
        $gjoni["duzina2"] = $row['duzina2'];
        $gjoni["sirina3"] = $row['sirina3'];
        $gjoni["velicina"] = $row['velicina'];

        $gjoni['clatitude'] = $row['latitude'];
        $gjoni['clongitude'] = $row['longitude'];
        $gjoni['cparkingLatitude'] = $row['parkingLatitude'];
        $gjoni['cparkingLongitude'] = $row['parkingLongitude'];

        $gjoni["brojOsoba"] = $row["brojOsoba"];
        $gjoni["brojDjece"] = $row["brojDjece"];
        $gjoni["kapacitetLezajeva"] = $row["kapacitetLezajeva"];

        $gjoni["pausal"] = $row['pausal'];
        $gjoni["osuncanost"] = $osuncanost[$row['osuncanostId']];
        $gjoni["parking"] = $row['parking'];
        $gjoni["wifi"] = $row['wifi'];
        $gjoni["mikrovalna"] = $row['mikrovalna'];
        $gjoni["odvodnja"] = $row['odvodnja'];
        $gjoni["struja6a"] = $row['struja6a'];
        $gjoni["struja10a"] = $row['struja10a'];
        $gjoni["struja16a"] = $row['struja16a'];
        $gjoni["parking"] = $row['parking'];
        $gjoni["voda"] = $row['voda'];
        $gjoni["satelitskaTv"] = $row['satelitskaTv'];
        $gjoni["kabelskaTv"] = $row['kabelskaTv'];
        $gjoni["perilicaPosuda"] = $row['perilicaPosuda'];
        $gjoni["perilicaRublja"] = $row['perilicaRublja'];
        $gjoni["klimaUredaj"] = $row['klimaUredaj'];
        $gjoni["toster"] = $row['toster'];
        $gjoni["pegla"] = $row['pegla'];
        $gjoni["rostilj"] = $row['rostilj'];
        $gjoni["bazen"] = $row['bazen'];
        $gjoni["jacuzzi"] = $row['jacuzzi'];
        $gjoni["dogsNotAllowed"] = $row['petsNotAllowed'];
        $gjoni["dogsAllowed"] = $row['petsAllowed'];
        $gjoni["brojOsoba"] = $row['brojOsoba'];
        $gjoni["brojDjece"] = $row['brojDjece'];
        $gjoni["napomena_hr"] = $row['napomena_hr'];
        $gjoni["napomena_en"] = $row['napomena_en'];
        $gjoni["napomena_de"] = $row['napomena_de'];
        $gjoni["napomena_it"] = $row['napomena_it'];
        $gjoni["napomena_si"] = $row['napomena_si'];
        $gjoni["napomena_pl"] = $row['napomena_pl'];
        $gjoni["napomena_ru"] = $row['napomena_ru'];
        $gjoni["napomena_nl"] = $row['napomena_nl'];
        $gjoni["panom"] = $row['slika'];
        $gjoni["images"] = $images;
    }

    echo json_encode($gjoni);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
