<?php
namespace Scotty\restinterface\querybuilder\read;

use \Scotty\database\DatabaseConnector;
use \Scotty\database\DatabaseException;
use \Scotty\database\DbHelper;
use \Scotty\restinterface\querybuilder\AssocBinder;
use \Scotty\restinterface\querybuilder\AbstractQueryBuilder;
use \Scotty\restinterface\querybuilder\BindParam;

class AbstractReadQueryBuilder extends AbstractQueryBuilder
{

    private $whereParts = array();

    private $orderByParts = array();

    private $limitPart = "";

    protected $bindParam;

    public function __construct($table, $request)
    {
        parent::__construct($table, $request);
        $this->extractIdToFilter();
        
    }

    private function extractIdToFilter()
    {
        if ($this->request->id != null) {
            $this->addEqualsWhere("id", $this->request->id);
        }
    }

    public function addEqualsWhere($attribute, $value)
    {
        $part = new EqualsWherePart($this);
        $part->where($attribute, $value);
        $this->addToWhereParts($part);
        return $this;
    }

    public function addLikeWhere($attribute, $value)
    {
        $part = new LikeWherePart($this);
        $part->like($attribute, $value);
        $this->addToWhereParts($part);
        return $this;
    }

    private function addToWhereParts(IWherePart $part)
    {
        $amount = count($this->whereParts);
        if ($amount > 0 && ! ($this->whereParts[$amount - 1] instanceof ConcatWherePart)) {
            $concatPart = new ConcatWherePart();
            $concatPart->setPart("AND");
            $this->whereParts[] = $concatPart;
        }
        $this->whereParts[] = $part;
    }

    public function addSimpleWhere($part)
    {
        $part = new SimpleWherePart($this);
        $part->setWherePart($part);
        $this->addToWhereParts($part);
        return $this;
    }

    public function andWhere()
    {
        $part = new ConcatWherePart($this);
        $part->setPart("AND");
        $this->whereParts[] = $part;
        return $this;
    }

    public function orWhere()
    {
        $part = new ConcatWherePart($this);
        $part->setPart("OR");
        $this->whereParts[] = $part;
        return $this;
    }

    public function openBrace()
    {
        $part = new ConcatWherePart($this);
        $part->setPart("(");
        $this->whereParts[] = $part;
        return $this;
    }

    public function closeBrace()
    {
        $part = new ConcatWherePart($this);
        $part->setPart(")");
        $this->whereParts[] = $part;
        return $this;
    }

    public function addOrderBy($attribute, $direction)
    {
        // according to http://stackoverflow.com/questions/4977898/check-for-valid-sql-column-name
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $attribute) !== 1) {
            $this->logger->warn("Detected sql injection attempt in addOrderBy for attribute value $attribute");
            throw new \RuntimeException("Detected sql injection attempt!");
        }
        if (! strtolower($direction) == 'asc' && ! strtolower($direction) == 'desc') {
            $direction == "ASC";
        }
        $this->orderByParts[] = "$attribute $direction";
        return $this;
    }

    /**
     * Be aware: This method does not prevent from sql injection.
     *
     * @param String $part            
     */
    public function addOrderByPart($part)
    {
        $this->orderByParts[] = $part;
        return $this;
    }

    public function build($db)
    {
        $this->addFiltersAndParams();
        $this->bindParam = new BindParam();
        $query = "SELECT " . $this->buildCommaSeparatedColumns() . " FROM " . $this->table;
        $query .= $this->buildWhere();
        $query .= $this->buildOrderBy();
        $query .= $this->limitPart;
        $statement = $db->prepare($query);
        DbHelper::throwExceptionOnError($statement, $db, $query);
        DbHelper::bindParams($statement, $this->bindParam);
        $this->logStatement($query, $this->bindParam->get());
        return $statement;
    }
    
    protected function buildCommaSeparatedColumns(){
        return "*";
    }
    
    protected function addFiltersAndParams()
    {
        $this->addFilters();
        $this->addSortParams();
        $this->addLimitParams();
    }
    
    private function addFilters()
    {
        $whereParam = $this->request->getRequestParamAsDecodedJson("filter");
        if ($whereParam != null) {
            foreach ($whereParam as $aParam) {
                $property = self::removeNamespaceFromFilterProperty($aParam->property);
                $this->addEqualsWhere($property, $aParam->value);
            }
        }
    }
    
    private function addSortParams()
    {
        $sortParams = $this->request->getRequestParamAsDecodedJson("sort");
        if ($sortParams != null) {
            foreach ($sortParams as $aParam) {
                $this->addOrderBy($aParam->property, $aParam->direction);
            }
        }
    }
    
    private function addLimitParams()
    {
        $start = $this->request->getRequestParam("start");
        $limit = $this->request->getRequestParam("limit");
        if (is_numeric($start) && is_numeric($limit)) {
            $this->setLimit($start, $limit);
        }
    }
    
    /**
     * A filter json comes like this:
     * filter:[{"property":"songserver.model.rubrik_id","value":3}]
     *
     * This function removes the namespaces from the property value:
     * songserver.model.rubrik_id --> rubrik_id
     *
     * @param String $property
     */
    public final static function removeNamespaceFromFilterProperty($property)
    {
        $parts = explode(".", $property);
        return $parts[count($parts) - 1];
    }

    public function determineTotalCountAndClose($statement)
    {
        if ($this->hasLimitPart()) {
            $statement->close();
            $statement = $this->buildCountIgnoreLimit();
            $statement->execute();
            DbHelper::throwExceptionOnStatementError($statement);
            $row = array();
            AssocBinder::bind($statement, $row);
            if ($statement->fetch()) {
                $count = $row['count'];
            } else {
                throw new DatabaseException("Determine total count failed.");
            }
            $statement->close();
        } else {
            $count = $statement->num_rows;
            $statement->close();
        }
        return $count;
    }

    /**
     * Builds a query to count all results.
     * If a limitPart is set, it will be ignored.
     */
    private function buildCountIgnoreLimit()
    {
        $this->bindParam = new BindParam();
        $query = "SELECT count(*) as count FROM " . $this->table;
        $query .= $this->buildWhere();
        $query .= $this->buildOrderBy();
        $this->logger->trace("Built count query: " . $query . "; Params: " . implode(", ", $this->bindParam->get()));
        $db = DatabaseConnector::db();
        $statement = $db->prepare($query);
        DbHelper::throwExceptionOnError($statement, $db, $query);
        DbHelper::bindParams($statement, $this->bindParam);
        return $statement;
    }

    private function buildWhere()
    {
        if (count($this->whereParts) === 0) {
            return "";
        }
        $whereString = " WHERE ";
        foreach ($this->whereParts as $part) {
            $this->addBindParams($part->getParamsToBind());
            $whereString .= $part->getPart();
        }
        return $whereString;
    }

    private function addBindParams($params)
    {
        foreach ($params as $param) {
            $this->bindParam->add($param[0], $param[1]);
        }
    }

    private function buildOrderBy()
    {
        if (count($this->orderByParts) === 0) {
            return "";
        }
        $orderByString = " ORDER BY " . implode(', ', $this->orderByParts);
        return $orderByString;
    }

    public function setLimit($start, $limit)
    {
        $this->limitPart = " LIMIT $start, $limit";
    }

    public function hasLimitPart()
    {
        if ($this->limitPart == "") {
            return false;
        } else {
            return true;
        }
    }

    public function modifyOrderByPart($partToModify, $modified)
    {
        for ($i = 0; $i < count($this->orderByParts); $i ++) {
            $part = $this->orderByParts[$i];
            // case-insensitive string comparison.
            if (strcasecmp($part, $partToModify) == 0) {
                $this->orderByParts[$i] = $modified;
                return;
            }
        }
    }
}

