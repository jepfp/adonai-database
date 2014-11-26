<?php
use_helper('Debug');

$arr = Array();
$categories = $sf_data->getRaw("rubriks");
foreach($categories as $category){
	$arr[] = $category->getOverviewArray();
}

$buecher = array(
	"records" => $arr,
	"totalCount" => $categories->count()
);
echo json_encode($buecher);

?>