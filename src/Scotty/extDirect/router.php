<?php
use \Scotty\security\SecInfoProvider;
use \Scotty\security\SecurityException;

// jep: Bogus means "gefälscht". It fakes the HTTP_RAW_POST_DATA if it is not set.
class BogusAction
{

    public $action;

    public $method;

    public $data;

    public $tid;
}

header('Content-type: text/javascript');

$isUpload = false;
$data = tryToReadInputStreamAsJson();
if ($data !== null) {
    // jep: for Content-Type: application/json
    header('Content-Type: text/javascript');
    //$data = json_decode(file_get_contents("php://input"));
} else {
    // jep: for forms
    // note from http://www.php.net/manual/de/ini.core.php#ini.always-populate-raw-post-data
    // $HTTP_RAW_POST_DATA ist bei enctype="multipart/form-data" nicht verfügbar.
    if (isFormRequest()) { // form post
        $isUpload = $_POST['extUpload'] == 'true';
        $data = new BogusAction();
        $data->action = $_POST['extAction'];
        $data->method = $_POST['extMethod'];
        $data->tid = isset($_POST['extTID']) ? $_POST['extTID'] : null; // not set for upload
        $data->data = array(
            $_POST,
            $_FILES
        );
    } else {
        die('Invalid request.');
    }
}

function tryToReadInputStreamAsJson(){
    $requestContent = file_get_contents("php://input");
    return json_decode($requestContent);
}

function isFormRequest()
{
    return isset($_POST['extAction']);
}

function doRpc($callerData)
{
    global $API;
    try {
        if (! isset($API[$callerData->action])) {
            throw new Exception('Call to undefined action: ' . $callerData->action);
        }
        
        $action = $callerData->action;
        $apiAction = $API[$action];
        $namespace = $apiAction['namespace'];
        $method = $callerData->method;
        
        // check if user is logged in (if he doesn't try to log in)
        // TODO: do a proper exclusion implementation
        if ($action != "Authentication" && $method != "register") {
            // for everything but login and registrations
            SecInfoProvider::throwErrorIfNotLoggedIn();
        }
        
        doAroundCalls($apiAction['before'], $callerData);
        
        $apiMethodDefinition = $apiAction['methods'][$method];
        if (! $apiMethodDefinition) {
            throw new Exception("Call to undefined method: $method on action $action");
        }
        doAroundCalls($apiMethodDefinition['before'], $callerData);
        
        $returnValue = array(
            'type' => 'rpc',
            'tid' => $callerData->tid,
            'action' => $action,
            'method' => $method,
            'success' => true // TODO: this is actually not needed by extjs but still asserted in tests. Remove.
        );
        
        // TODO jep: Check possible security issue here.
        $fullyQualifiedAction = $namespace . $action;
        $actionClass = new $fullyQualifiedAction();
        if (isset($apiMethodDefinition['len'])) {
            $params = isset($callerData->data) && is_array($callerData->data) ? $callerData->data : array();
        } else {
            $params = array(
                $callerData->data
            );
        }
        
        $returnValue['result'] = call_user_func_array(array(
            $actionClass,
            $method
        ), $params);
        
        doAroundCalls($apiMethodDefinition['after'], $callerData, $returnValue);
        doAroundCalls($apiAction['after'], $callerData, $returnValue);
    } catch (SecurityException $e) {
        handleException($e, $returnValue);
    } catch (Exception $e) {
        handleException($e, $returnValue);
    }
    return $returnValue;
}

// jep: This method does method calls (before and after) the actual method call if it is configured so. Never used yet.
function doAroundCalls(&$fns, &$callerData, &$returnData = null)
{
    if (! $fns) {
        return;
    }
    if (is_array($fns)) {
        foreach ($fns as $f) {
            $f($callerData, $returnData);
        }
    } else {
        $fns($callerData, $returnData);
    }
}

function handleException($e, &$returnValue)
{
    $returnValue['type'] = 'exception';
    $returnValue['message'] = $e->getMessage();
    $returnValue['where'] = $e->getTraceAsString();
    $returnValue['success'] = false; // TODO: this is actually not needed by extjs but still asserted in tests. Remove.
    if (isFormRequest()) {
        // because ext js doesn't seem to handle exceptions in the same way when
        // performing e form post, we have to add the information to the result-node as well
        $returnValue['result']['success'] = false;
        $returnValue['result']['message'] = $e->getMessage();
    }
}

$response = null;
if (is_array($data)) {
    $response = array();
    foreach ($data as $d) {
        $response[] = doRpc($d);
    }
} else {
    $response = doRpc($data);
}
if (isFormRequest() && $isUpload) {
    echo '<html><body><textarea>';
    echo json_encode($response);
    echo '</textarea></body></html>';
} else {
    echo json_encode($response);
}