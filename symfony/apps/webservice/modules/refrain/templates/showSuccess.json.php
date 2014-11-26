<?php

$refrain = $sf_data->getRaw("refrain")->getShowArray();

echo json_encode($refrain);

?>