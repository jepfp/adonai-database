<?php

$refrains = $sf_data->getRaw("refrains");
$arr = Array();

foreach ($refrains as $refrain){
	$arr[] = $refrain->getShowArray();
}

echo json_encode($arr);

?>
