<?php
namespace Scotty\restinterface;

/**
 * @class Response
 * A simple JSON Response class.
 */
class Response
{

    public $success, $data, $message, $errors, $tid, $trace, $totalCount;
    
    public $type;
    
    public function __construct()
    {
        $this->type = "response";
        $this->success = true;
        $this->message = "Success";
    }

    public function to_json()
    {
        $json = json_encode(array(
            'success' => $this->success,
            'message' => $this->message,
            'type' => $this->type,
            'data' => $this->data,
            'totalCount' => $this->totalCount
        ));
        JsonVerifier::verifyNoJsonError();
        return $json;
    }
    
    public function isException(){
        return $this->type == "exception";
    }
}
