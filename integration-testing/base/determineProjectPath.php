<?php
require_once("../../src/bootstrap.php");

$arr = array("projectPath" => $projectConfiguration->getProjectPath());

header('Content-type: text/javascript');
echo json_encode($arr);
?>
