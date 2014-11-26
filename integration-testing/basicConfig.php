<?php
require_once("../src/bootstrap.php");

$arr = $projectConfiguration->getWholeConfig();

echo json_encode($arr);
?>