<?php
namespace Scotty\restinterface\requestparamsparser;

use Scotty\restinterface\JsonVerifier;
class ApplicationJsonParser implements RequestParamsParser
{

    public function parseParams()
    {
        $raw = $this->readRawHttpContent();
        $params = json_decode($raw);
        JsonVerifier::verifyNoJsonError();
        return $params;
    }

    private function readRawHttpContent()
    {
        $raw = "";
        $httpContent = fopen('php://input', 'r');
        while ($kb = fread($httpContent, 1024)) {
            $raw .= $kb;
        }
        fclose($httpContent);
        return $raw;
    }
}

