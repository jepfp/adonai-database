<?php

$db = new SQLite3('adonaiWebDb.sqlite');

$results = $db->query('SELECT * FROM lied ORDER BY Nr');
while ($row = $results->fetchArray()) {
  echo 'Nr: '.$row['Nr'].' | Titel: '.$row['Titel'].'<br />';
//    var_dump($row);
}
?>