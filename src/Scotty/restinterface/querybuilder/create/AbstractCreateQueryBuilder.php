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

class AbstractCreateQueryBuilder extends AbstractQueryBuilder
{

    /**
     * 
     * @var BindParam
     */
    protected $bindParam;

    /**
     *
     * @var AbstractDTO
     */
    private $dto;

    public function __construct($table, $request)
    {
        parent::__construct($table, $request);
        
        $this->dto = $this->instantiateDTO();
    }

    private function instantiateDTO()
    {
        $fullyQualifiedName = DynamicClassResolver::resolveDtoClass($this->request->controller);
        return new $fullyQualifiedName($this->request->params);
    }

    public function build($db)
    {
        $this->bindParam = new BindParam();
        $fields = $this->dto->getAllFieldNames();
        $query = "INSERT INTO " . $this->table . " (" . implode(", ", $fields) . ") VALUES (?" . str_repeat(", ?", count($fields) - 1) . ")";
        $this->buildValues();
        $statement = $db->prepare($query);
        DbHelper::bindParams($statement, $this->bindParam);
        DbHelper::throwExceptionOnError($statement, $db, $query);
        $this->logStatement($query, $this->bindParam->get());
        return $statement;
    }

    private function buildValues()
    {
        $keyValuePairs = $this->dto->getAllKeyValuePairs();
        foreach ($keyValuePairs as $key => $value) {
            $this->bindParam->add("s", $value);
        }
    }

    public function determineTotalCountAndClose($statement)
    {
        $count = $statement->affected_rows;
        $statement->close();
        return $count;
    }
}

