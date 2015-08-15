<?php
namespace Scotty\restinterface;

class ResponseSerializer
{

    private static $logger;

    private static function getLogger()
    {
        if (! self::$logger) {
            self::$logger = \Logger::getLogger("main");
        }
        return self::$logger;
    }

    /**
     *
     * @param Request $request            
     * @param Response $response            
     */
    public static function serializeResponse($request, $response)
    {
        if ($response->isException()) {
            // TODO: Clean implementation of Responses with status codes and server or client side errors
            self::serializeJson($response);
        } elseif (self::isBinaryController($request->controller, $response) && self::isGetRequest($request)) {
            self::serializeBinary($response);
        } else {
            self::serializeJson($response);
        }
    }

    private static function isBinaryController($controller, $response)
    {
        return $controller == "file";
    }

    private static function isGetRequest($request)
    {
        return strcasecmp($request->method, "GET") === 0;
    }

    private static function serializeBinary($response)
    {
        $fileAndItsMetadata = self::findOnlyElement($response);
        header('Content-Description: File Transfer');
        // TODO: Implement more general when adding other types than pdf. For unknown use header('Content-Type: application/octet-stream');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $fileAndItsMetadata["builtFilename"] . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Content-Length: ' . $fileAndItsMetadata["filesize"]);
        echo $fileAndItsMetadata["data"];
    }

    private static function serializeJson($response)
    {
        echo $response->to_json();
    }

    private static function findOnlyElement($response)
    {
        $data = $response->data;
        if (count($data) == 0) {
            throw new \Exception("File not found.");
        } elseif (count($data) > 1) {
            // This should never happen as it is checked in FileDAO already.
            throw new \Exception("More than one file found.");
        } else {
            return $data[0];
        }
    }
}

?>