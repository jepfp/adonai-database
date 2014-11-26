<?php
require_once("../../../src/bootstrap.php");

$projectLoader = new Scotty\project\ProjectLoader("./", "default");

$arr = array("subdomain" => $projectLoader->getSubdomain());

header('Content-type: text/javascript');
echo json_encode($arr);
?>