<?php
namespace Scotty\restinterface\querybuilder;

use Scotty\auth\UserDTO;
use Scotty\changebacktrack\ChangeBacktrack;

abstract class AbstractQueryBuilder
{

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
        $this->logger->trace("Built query: " . $query . "; Params: " . ($params != null ? implode(", ", $params) : null));
        ChangeBacktrack::logStatement($this->table, $query, $params);
    }
}

?>