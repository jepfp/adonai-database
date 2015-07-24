<?php
namespace Scotty\restinterface\requestparamsparser;

use Scotty\restinterface\JsonVerifier;

class MultipartFormDataParser implements RequestParamsParser
{

    public function parseParams()
    {
        $params = $_POST;
        return $params;
    }
}

