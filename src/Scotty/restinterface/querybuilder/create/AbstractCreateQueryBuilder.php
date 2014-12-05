<?php
namespace Scotty\restinterface\querybuilder\create;

use \Scotty\database\DatabaseConnector;
use \Scotty\database\DatabaseException;
use \Scotty\database\DbHelper;
use \Scotty\restinterface\querybuilder\AssocBinder;
use \Scotty\restinterface\querybuilder\AbstractQueryBuilder;
use \Scotty\restinterface\querybuilder\BindParam;
use Scotty\restinterface\DynamicClassResolver;
use Scotty\restinterface\dto\AbstractDTO;
use Scotty\restinterface\querybuilder\AbstractModificationQueryBuilder;

class AbstractCreateQueryBuilder extends AbstractModificationQueryBuilder
{

    protected function buildQueryString($fields)
    {
        return "INSERT INTO " . $this->table . " (" . implode(", ", $fields) . ") VALUES (?" . str_repeat(", ?", count($fields) - 1) . ")";
    }
}

