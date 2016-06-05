<?php
namespace Scotty\restinterface\querybuilder\create;

use Scotty\changebacktrack\ChangeBacktrack;

class FileCreateQueryBuilder extends AbstractCreateQueryBuilder
{

    const MAX_SIZE_IN_BYTES_OF_A_PARAM_TO_BE_LOGGED = 300;

    protected function logStatement($query, $params)
    {
        $cuttedParams = $this->cutParamsForLogging($params);
        $this->logger->trace("Built query: " . $query . "; Params: " . ($cuttedParams != null ? implode(", ", $cuttedParams) : null));
        ChangeBacktrack::logStatement($this->table, $query, $cuttedParams);
    }

    private function cutParamsForLogging($params)
    {
        // This is necessary because of the following error
        // Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 14771787 bytes)
        // in src\lib\log4php\appenders\LoggerAppenderPDO.php on line 164.
        if ($params == null) {
            return $params;
        }
        foreach ($params as $p) {
            if (strlen($p) > self::MAX_SIZE_IN_BYTES_OF_A_PARAM_TO_BE_LOGGED) {
                $cuttedParams[] = "[CUTTED PARAM > 300 Bytes FOR LOGGING BY SCOTTY]";
            } else {
                $cuttedParams[] = $p;
            }
        }
        return $cuttedParams;
    }
}

