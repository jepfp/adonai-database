<?php
use Scotty\restinterface\DAOFactory;
use Scotty\restinterface\ResponseSerializer;
require ('bootstrap.php');

try {
    $request = Scotty\restinterface\Request::createFromHttpRequest();
    $dao = DAOFactory::createDAO($request->controller);
    $response = $dao->dispatch($request);
    ResponseSerializer::serializeResponse($request, $response);
} catch (Exception $ex) {
    $logger->error("Allgemeiner Fehler in rest-interface: " . $ex->getMessage(), $ex);
    http_response_code(500);
    echo '{"success": false, "message" : "Allgemeiner Fehler in rest-interface.", type: "exception"}';
}
