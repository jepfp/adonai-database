<?php
//The only purpose of this file is to check, if mysqlnd is used as a driver.
$hasMySQL = false;
$hasMySQLi = false;
$withMySQLnd = false;

if (function_exists('mysql_connect')) {
    $hasMySQL = true;
    $sentence.= "(Deprecated) MySQL <b>is installed</b> ";
} else 
    $sentence.= "(Deprecated) MySQL <b>is not</b> installed ";

if (function_exists('mysqli_connect')) {
    $hasMySQLi = true;
    $sentence.= "and the new (improved) MySQL <b>is installed</b>. ";
} else
    $sentence.= "and the new (improved) MySQL <b>is not installed</b>. ";

if (function_exists('mysqli_get_client_stats')) {
    $withMySQLnd = true;
    $sentence.= "This server is using MySQLnd as the driver.";
} else
    $sentence.= "This server is using libmysqlclient as the driver.";

echo $sentence;

$db = new PDO('mysql:host=philippjenni.ch;dbname=philippjenni_testonai_testonai', 'philippjenni_tt', 'PUT RIGHT PASSWORD HERE');
if (strpos($pdo->getAttribute(PDO::ATTR_CLIENT_VERSION), 'mysqlnd') !== false) {
    echo 'PDO MySQLnd enabled!';
}