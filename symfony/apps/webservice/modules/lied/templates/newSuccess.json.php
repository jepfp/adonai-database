<?php

$song = $sf_data->getRaw("lied")->getShowArray();
$song["songbooks"] = $sf_data->getRaw("lied")->getAllSongbooksWithNumberArray();
$song["verses"] = null; //can stay empty.

echo json_encode($song);

?>