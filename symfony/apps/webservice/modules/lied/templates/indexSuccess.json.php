<?php
$arr = Array();
$pager = $sf_data->getRaw("pager");
$liederbuch = $sf_data->getRaw("liederbuch")->getOverviewArray();

foreach($pager->getResults() as $lied){
	$arr[] = $lied->getOverviewArray();
}

$dbLogger = Logger::getLogger("dbLogger");
if(get_slot("quicksearch") != null){
	$dbLogger->info("lied->index with quicksearch='" . get_slot("quicksearch") . "'; results=" . $pager->getNbResults());
}else{
	$dbLogger->info("lied->index results=" . $pager->getNbResults() . " (user=" . $_SESSION["email"] . ", id=" . $_SESSION["id"] . ")");
}

$songtable = array(
		"songtable" => $arr,
		"totalCount" => $pager->getNbResults(),
		"liederbuch" => $liederbuch
);
echo json_encode($songtable);

?>