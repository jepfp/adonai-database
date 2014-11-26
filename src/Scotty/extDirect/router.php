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

$isForm = false;
$isUpload = false;
if (isset($HTTP_RAW_POST_DATA)) {
    // jep: for Content-Type: application/json
    header('Content-Type: text/javascript');
    $data = json_decode($HTTP_RAW_POST_DATA);
} else {
    // jep: for forms
    // note from http://www.php.net/manual/de/ini.core.php#ini.always-populate-raw-post-data
    // $HTTP_RAW_POST_DATA ist bei enctype="multipart/form-data" nicht verfügbar.
    if (isset($_POST['extAction'])) { // form post
        $isForm = true;
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
        if ($action != "Authentication" &&         // for login
        $method != "register")         // for registrations
        {
            SecInfoProvider::throwErrorIfNotLoggedIn();
        }
        
        doAroundCalls($apiAction['before'], $callerData);
        
        $apiMethodDefinition = $apiAction['methods'][$method];
        if (! $apiMethodDefinition) {
            throw new Exception("Call to undefined method: $method on action $action");
        }
        doAroundCalls($apiMethodDefinition['before'], $callerData);
        
        $r = array(
            'type' => 'rpc',
            'tid' => $callerData->tid,
            'action' => $action,
            'method' => $method,
            'success' => true //if there is a successful method call, success is true
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
        
        $r['result'] = call_user_func_array(array(
            $actionClass,
            $method
        ), $params);
        
        doAroundCalls($apiMethodDefinition['after'], $callerData, $r);
        doAroundCalls($apiAction['after'], $callerData, $r);
    } catch (SecurityException $e) {
        $r['success'] = false;
        $r['type'] = 'exception';
        $r['message'] = $e->getMessage();
    } catch (Exception $e) {
        $r['success'] = false;
        $r['type'] = 'exception';
        $r['message'] = $e->getMessage();
        $r['where'] = $e->getTraceAsString();
    }
    return $r;
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

$response = null;
if (is_array($data)) {
    $response = array();
    foreach ($data as $d) {
        $response[] = doRpc($d);
    }
} else {
    $response = doRpc($data);
}
if ($isForm && $isUpload) {
    echo '<html><body><textarea>';
    echo json_encode($response);
    echo '</textarea></body></html>';
} else {
    echo json_encode($response);
}