<?php
$versioncontrol['lastUpdate'] = $newestLied["updated_at"];
$versioncontrol['song'] = $sf_data->getRaw("newestLied")->getShowArray();

echo json_encode($versioncontrol);

?>