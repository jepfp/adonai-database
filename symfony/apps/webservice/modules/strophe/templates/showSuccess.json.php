<?php

$verse = $sf_data->getRaw("liedtext")->getShowArray();
//$song["songbooks"] = $sf_data->getRaw("lied")->getAllSongbooksWithNumberArray();

echo json_encode($verse);

?>