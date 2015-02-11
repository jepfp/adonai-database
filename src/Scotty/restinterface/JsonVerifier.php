<?php
namespace Scotty\restinterface;

class JsonVerifier
{

    public static function verifyNoJsonError()
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
}

?>