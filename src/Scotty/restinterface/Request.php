<?php
/*
 * From ext-4.2.0-gpl\examples\restful\remote\lib
 */
namespace Scotty\restinterface;

use Scotty\restinterface\requestparamsparser\ParamsParserFactory;

class Request
{

    public $method, $controller, $action, $id, $params;

    public static function createFromHttpRequest()
    {
        $instance = new self();
        $instance->method = $_SERVER["REQUEST_METHOD"];
        $instance->parseParams();
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

    private function parseParams()
    {
        $parser = ParamsParserFactory::createParser();
        $this->params = $parser->parseParams();
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

