<?php
namespace Scotty\restinterface\requestparamsparser;

class ParamsParserFactory
{

    private static $logger;

    public static function createParser()
    {
        $parser;
        if (isset($_SERVER["CONTENT_TYPE"])) {
            $contentType = $_SERVER["CONTENT_TYPE"];
            if (self::containsCaseInsensitive($contentType, "application/json")) {
                $parser = new ApplicationJsonParser();
            } elseif (self::containsCaseInsensitive($contentType, "multipart/form-data")) {
                $parser = new MultipartFormDataParser();
            } else {
                self::getLogger()->debug("No request params parser found for: " . $contentType);
                $parser = new NullParser();
            }
        } else {
            $parser = new NullParser();
        }
        self::getLogger()->trace("Using the following request params parser: " . get_class($parser));
        return $parser;
    }

    private static function containsCaseInsensitive($contentType, $needle)
    {
        return stristr($contentType, $needle) !== false;
    }

    private static function getLogger()
    {
        if (! self::$logger) {
            self::$logger = \Logger::getLogger("main");
        }
        return self::$logger;
    }
}

