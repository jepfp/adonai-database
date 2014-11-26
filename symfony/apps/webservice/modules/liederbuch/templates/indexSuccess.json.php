<?php
use_helper('Debug');

$arr = Array();
$liederbuchs = $sf_data->getRaw("liederbuchs");
foreach($liederbuchs as $liederbuch){
	$arr[] = $liederbuch->getOverviewArray();
}

//find out the active liederbuch
$liederbuchId = $sf_user->getAttribute("liederbuchId");
if($liederbuchId == null){
	$liederbuchId = Doctrine_Core::getTable('Liederbuch')
		->getDefaultLiederbuch()
		->getId();
	$sf_user->setAttribute("liederbuchId", $liederbuchId);
	logMessage("jep: Liederbuch with id " . $liederbuchId . " was taken as the default liederbuch because it's the first one.");
}

$buecher = array(
"records" => $arr,
"totalCount" => $liederbuchs->count(),
"active" => $liederbuchId
);
echo json_encode($buecher);

?>