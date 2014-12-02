<?php
namespace Scotty\restinterface;

class Request
{

    public $method, $controller, $action, $id, $params;

    public static function createFromHttpRequest()
    {
        $instance = new self();
        $instance->method = $_SERVER["REQUEST_METHOD"];
        $instance->parseRequest();
        $instance->parsePathInfo();
        return $instance;
    }

    public static function create($method, $controller, $action, $id)
    {
        $instance = new self();
        $instance->method = $method;
        $instance->controller = $controller;
        $instance->action = $action;
        $instance->id = $id;
        return $instance;
    }

    protected function parseRequest()
    {
        if ($this->method == 'PUT') { // <-- Have to jump through hoops to get PUT data
            $raw = '';
            $httpContent = fopen('php://input', 'r');
            while ($kb = fread($httpContent, 1024)) {
                $raw .= $kb;
            }
            fclose($httpContent);
            $params = array();
            parse_str($raw, $params);
            
            if (isset($params['data'])) {
                $this->params = json_decode(stripslashes($params['data']));
            } else {
                $params = json_decode(stripslashes($raw));
                $this->params = $params;
            }
        } else {
            // grab JSON data if there...
            $this->params = (isset($_REQUEST['data'])) ? json_decode(stripslashes($_REQUEST['data'])) : null;
            
            if (isset($_REQUEST['data'])) {
                $this->params = json_decode(stripslashes($_REQUEST['data']));
            } else {
                $raw = '';
                $httpContent = fopen('php://input', 'r');
                while ($kb = fread($httpContent, 1024)) {
                    $raw .= $kb;
                }
                $params = json_decode(stripslashes($raw));
                if ($params) {
                    $this->params = $params;
                }
            }
        }
        $this->verifyNoJsonError();
    }

    private function verifyNoJsonError()
    {
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new \RuntimeException("JSON parse error: max stack depth error.");
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new \RuntimeException("JSON parse error: Unexpected control character found.");
                break;
            case JSON_ERROR_SYNTAX:
                throw new \RuntimeException("JSON parse error: Syntax error.");
                break;
            case JSON_ERROR_UTF8:
                throw new \RuntimeException("JSON parse error: UTF8 error.");
                break;
            case JSON_ERROR_NONE:
                break;
            default:
                throw new \RuntimeException("JSON parse error: Unknown error.");
                break;
        }
    }

    private function parsePathInfo()
    {
        if (isset($_SERVER["PATH_INFO"])) {
            $cai = '/^\/(\w+)\/([a-z]+)\/([0-9]+)$/'; // /controller/action/id
            $ca = '/^\/(\w+)\/([a-z]+)$/'; // /controller/action
            $ci = '/^\/(\w+)\/([0-9]+)$/'; // /controller/id
            $c = '/^\/(\w+)$/'; // /controller
            $i = '/^\/([0-9]+)$/'; // /id
            $matches = array();
            if (preg_match($cai, $_SERVER["PATH_INFO"], $matches)) {
                $this->controller = $matches[1];
                $this->action = $matches[2];
                $this->id = $matches[3];
            } elseif (preg_match($ca, $_SERVER["PATH_INFO"], $matches)) {
                $this->controller = $matches[1];
                $this->action = $matches[2];
            } elseif (preg_match($ci, $_SERVER["PATH_INFO"], $matches)) {
                $this->controller = $matches[1];
                $this->id = $matches[2];
            } elseif (preg_match($c, $_SERVER["PATH_INFO"], $matches)) {
                $this->controller = $matches[1];
            } elseif (preg_match($i, $_SERVER["PATH_INFO"], $matches)) {
                $this->id = $matches[1];
            }
        }
    }

    public function getRequestParamAsDecodedJson($key)
    {
        if (isset($_REQUEST[$key])) {
            return json_decode($_REQUEST[$key]);
        } else {
            return null;
        }
    }

    public function getRequestParam($key)
    {
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        } else {
            return null;
        }
    }
}

