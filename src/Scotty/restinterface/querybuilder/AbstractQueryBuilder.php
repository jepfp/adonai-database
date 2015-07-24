<?php
namespace Scotty\restinterface\querybuilder;

use Scotty\auth\UserDTO;
use Scotty\changebacktrack\ChangeBacktrack;

abstract class AbstractQueryBuilder
{

    const MAX_SIZE_IN_BYTES_OF_A_PARAM_TO_BE_LOGGED = 2000;

    protected $table;

    protected $logger;

    protected $request;

    public function __construct($table, $request)
    {
        $this->logger = \Logger::getLogger("main");
        $this->table = $table;
        $this->request = $request;
    }

    abstract public function determineTotalCountAndClose($statement);

    abstract public function build($db);

    protected function logStatement($query, $params)
    {
        $cuttedParams = $this->cutParamsForLogging($params);
        $this->logger->trace("Built query: " . $query . "; Params: " . ($cuttedParams != null ? implode(", ", $cuttedParams) : null));
        ChangeBacktrack::logStatement($this->table, $query, $cuttedParams);
    }

    private function cutParamsForLogging($params)
    {
        if ($params == null) {
            return $params;
        }
        foreach ($params as $p) {
            if (mb_strlen($p) > 2000) {
                $cuttedParams[] = mb_strcut($p, 0, self::MAX_SIZE_IN_BYTES_OF_A_PARAM_TO_BE_LOGGED) . "[CUTTED FOR LOGGING BY SCOTTY]";
            } else {
                $cuttedParams[] = $p;
            }
        }
        return $cuttedParams;
    }
}

?>