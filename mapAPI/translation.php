<?php
$database   = 'ca_' . $_GET['group'];
require "config_cikat.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$db = new PDO($dsn, $username, $password);
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

$db->query("SET NAMES 'utf8'");

  $sql = "SELECT * FROM prijevodi WHERE deleted = 0 ";


# Try query or error
$dbquery = $db->query($sql);
if (!$dbquery) {
    echo 'An SQL error occured.\n';
    exit;
}

// var brandColors = {
//   "Premium": [{
//     "color": "#f2c48d",
//     "border": "#b39169"
//   }],
//   "Family": [{
//     "color": "#f28586",
//     "border": "#c26b6c"
//   }],
//   "Deluxe": [{
//     "color": "#add8e6",
//     "border": "#31708f"
//   }]
// };

$colors2 = [];
$gjoni = [];

# Loop through rows to build feature arrays
while ($row = $dbquery->fetch(PDO::FETCH_ASSOC)) {
    $pero = [];

    $temp_en = [];
    $temp_en = array($row['en'], $row['en_m']);

    $temp_hr = [];
    $temp_hr = array($row['hr'], $row['hr_m']);

    $temp_de = [];
    $temp_de = array($row['de'], $row['de_m']);
    
    $temp_it = [];
    $temp_it = array($row['it'], $row['it_m']);
    
    $temp_nl = [];
    $temp_nl = array($row['nl'], $row['nl_m']);
    
    $temp_ru = [];
    $temp_ru = array($row['ru'], $row['ru_m']);
    
    $temp_si = [];
    $temp_si = array($row['si'], $row['si_m']);
    
    $temp_pl = [];
    $temp_pl = array($row['pl'], $row['pl_m']);    
    //var_dump($temp_hr);

    if ($row['en_m'] != '') {
      $colors2["en"] = $temp_en;
    } else {
      $colors2["en"] = $row['en'];
    }

    if ($row['hr_m'] != '') {
      $colors2["hr"] = $temp_hr;
    } else {
      $colors2["hr"] = $row['hr'];
    }

    if ($row['de_m'] != '') {
      $colors2["de"] = $temp_de;
    } else {
      $colors2["de"] = $row['de'];
    }
    
    if ($row['si_m'] != '') {
      $colors2["si"] = $temp_si;
    } else {
      $colors2["si"] = $row['si'];
    }
    
    if ($row['ru_m'] != '') {
      $colors2["ru"] = $temp_ru;
    } else {
      $colors2["ru"] = $row['ru'];
    }
    
    if ($row['nl_m'] != '') {
      $colors2["nl"] = $temp_nl;
    } else {
      $colors2["nl"] = $row['nl'];
    }    

    if ($row['pl_m'] != '') {
      $colors2["pl"] = $temp_pl;
    } else {
      $colors2["pl"] = $row['pl'];
    }        

    if ($row['it_m'] != '') {
      $colors2["it"] = $temp_it;
    } else {
      $colors2["it"] = $row['it'];
    }        

    $colors2["de"] = $row['de'];
    $colors2["nl"] = $row['nl'];
    $colors2["it"] = $row['it'];
    $colors2["ru"] = $row['ru'];
    $colors2["pl"] = $row['pl'];
    $colors2["si"] = $row['si'];


    array_push($pero, $colors2);
    $gjoni[$row['text_string']] = $pero;


}

echo json_encode($gjoni, JSON_UNESCAPED_UNICODE);

header('Content-type: application/json');
//echo json_encode($class);
$conn = NULL;
?>
