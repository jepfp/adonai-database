<?php
namespace Scotty\restinterface\querybuilder\delete;

use \Scotty\restinterface\querybuilder\AbstractQueryBuilder;
use Scotty\database\DbHelper;

abstract class AbstractDeleteQueryBuilder extends AbstractQueryBuilder
{

    private $idsToDelete = array();

    public function __construct($table, $request)
    {
        parent::__construct($table, $request);
        $this->extractIdsToDelete($request);
    }

    private function extractIdsToDelete($request)
    {
        // for now we just extract the REST request id.
        // We could parse other request parameters here later in order
        // to be able to extract multiple ids.
        if ($request->id != null) {
            $this->addIdToDelete($request->id);
        }
    }

    private function addIdToDelete($idToDelete)
    {
        if (! is_numeric($idToDelete)) {
            throw new \Exception("Invalid id to delete. Not a number.");
        }
        $this->idsToDelete[] = $idToDelete;
        return $this;
    }

    public function build($db)
    {
        $this->validateIdsNotEmpty();
        $query = "DELETE FROM " . $this->table;
        $query .= $this->buildWhere();
        $statement = $db->prepare($query);
        DbHelper::throwExceptionOnError($statement, $db, $query);
        $this->logger->trace("Built query: " . $query);
        $this->logStatement($query, null);
        return $statement;
    }

    private function validateIdsNotEmpty()
    {
        if (empty($this->idsToDelete)) {
            throw new \RuntimeException("List of entries to delete is empty!");
        }
    }

    private function buildWhere()
    {
        if (count($this->idsToDelete) === 0) {
            throw new \Exception("There must be at least one id to delete");
        }
        $whereString = " WHERE id = " . implode(" OR id = ", $this->idsToDelete);
        return $whereString;
    }

    public function determineTotalCountAndClose($statement)
    {
        $count = $statement->affected_rows;
        $statement->close();
        return $count;
    }
}

