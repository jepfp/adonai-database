<?php

$song = $sf_data->getRaw("lied")->getShowArray();
$song["songbooks"] = $sf_data->getRaw("lied")->getAllSongbooksWithNumberArray();
$song["verses"] = $sf_data->getRaw("lied")->getAllVerseIds();

echo json_encode($song);

?>