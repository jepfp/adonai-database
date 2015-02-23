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

    public static function serializeResponse($controller, $response)
    {
        if ($response->isException()) {
            // Internal Server Error
            // TODO: Clean implementation of Responses with status codes and client side errors
            http_response_code(500);
            self::serializeJson($response);
        } elseif (self::isBinaryController($controller, $response)) {
            self::serializeBinary($response);
        } else {
            self::serializeJson($response);
        }
    }

    private static function isBinaryController($controller, $response)
    {
        return $controller == "file";
    }

    private static function serializeBinary($response)
    {
        $fileAndItsMetadata = self::findOnlyElement($response);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileAndItsMetadata["builtFilename"] . '"');
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