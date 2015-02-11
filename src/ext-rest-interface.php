<?php
use Scotty\restinterface\DAOFactory;
header('Content-type: text/javascript');
require ('bootstrap.php');

try {
    $request = Scotty\restinterface\Request::createFromHttpRequest();
    $dao = DAOFactory::createDAO($request->controller);
    $response = $dao->dispatch($request);
    if($response->type == "exception"){
        //Internal Server Error
        //TODO: Clean implementation of Responses with status codes and client side errors
        http_response_code(500);
    }
    echo $response->to_json();
    //TODO: Check for json error here
} catch (Exception $ex) {
    $logger->error("Allgemeiner Fehler in rest-interface: " . $ex->getMessage(), $ex);
    echo '{"success": false, "message" : "Allgemeiner Fehler in rest-interface."}';
}
