<?php
$song = $sf_data->getRaw("lied")->getOverviewArray($liederbuchId);

$songtable = array(
		"songtable" => array($song)
);

echo json_encode($songtable);

?>