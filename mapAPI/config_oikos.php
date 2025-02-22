<?php
$server     = 'localhost';
$username   = 'denis';
$password   = 'oM36303690!';
//$database   = 'ca_jadranka'; //BITNO!!!! ovo budimo maknuli i parametar se dobiva iz GETa ili POSTa na koju bazu se spajamo

$dsn        = "mysql:host=$server;dbname=$database";

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'denis');
define('DB_PASSWORD', 'oM36303690!');
define('DB_DATABASE', 'oikospag');
$db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

?>
