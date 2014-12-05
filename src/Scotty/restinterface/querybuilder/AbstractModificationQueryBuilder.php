<?php
namespace Scotty\restinterface\querybuilder;

use \Scotty\database\DatabaseConnector;
use \Scotty\database\DatabaseException;
use \Scotty\database\DbHelper;
use \Scotty\restinterface\querybuilder\AssocBinder;
use \Scotty\restinterface\querybuilder\AbstractQueryBuilder;
use \Scotty\restinterface\querybuilder\BindParam;
use Scotty\restinterface\DynamicClassResolver;
use Scotty\restinterface\dto\AbstractDTO;

abstract class AbstractModificationQueryBuilder extends AbstractQueryBuilder
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
        $query = $this->buildQueryString($fields);
        $this->buildValues();
        $statement = $db->prepare($query);
        DbHelper::throwExceptionOnError($statement, $db, $query);
        DbHelper::bindParams($statement, $this->bindParam);
        $this->logStatement($query, $this->bindParam->get());
        return $statement;
    }
    
    abstract protected function buildQueryString($fields);

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

