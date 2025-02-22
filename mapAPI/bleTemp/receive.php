<?php
// Get the raw POST data
$rawData = file_get_contents("php://input");

// Decode the JSON data
$jsonData = json_decode($rawData, true);

// Dump the decoded JSON data
var_dump($jsonData);
?>
