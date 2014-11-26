<?php
use Scotty\restinterface\DAOFactory;
header('Content-type: text/javascript');
require ('bootstrap.php');

try {
    $request = Scotty\restinterface\Request::createFromHttpRequest();
    $dao = DAOFactory::createDAO($request->controller);
    $response = $dao->dispatch($request);
    echo $response->to_json();
} catch (Exception $ex) {
    $logger->error("Allgemeiner Fehler in rest-interface: " . $ex->getMessage());
    echo '{"success": false, "message" : "Allgemeiner Fehler in rest-interface."}';
}
