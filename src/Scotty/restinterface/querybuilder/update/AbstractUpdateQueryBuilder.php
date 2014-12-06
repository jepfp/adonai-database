<?php
namespace Scotty\restinterface\querybuilder\update;

use \Scotty\database\DatabaseConnector;
use \Scotty\database\DatabaseException;
use \Scotty\database\DbHelper;
use \Scotty\restinterface\querybuilder\AssocBinder;
use \Scotty\restinterface\querybuilder\AbstractQueryBuilder;
use \Scotty\restinterface\querybuilder\BindParam;
use Scotty\restinterface\DynamicClassResolver;
use Scotty\restinterface\dto\AbstractDTO;
use Scotty\restinterface\querybuilder\AbstractModificationQueryBuilder;

class AbstractUpdateQueryBuilder extends AbstractModificationQueryBuilder
{

    protected function buildQueryString()
    {
        $fields = $this->dto->getAllFieldNamesInParams();
        return "UPDATE " . $this->table . " SET " . implode(" = ?, ", $fields) . "= ? WHERE id = ?";
    }

    protected function buildValues()
    {
        // Only get the key value pairs which were sent in the request.
        $keyValuePairs = $this->dto->getAllKeyValuePairsInParams();
        foreach ($keyValuePairs as $key => $value) {
            $this->bindParam->add("s", $value);
        }
        $this->bindParam->add("s", $this->getIdAndThrowExceptionIfNotValid());
    }

    private function getIdAndThrowExceptionIfNotValid()
    {
        $id = $this->request->id;
        if ($id != null && is_numeric($id) && $id > 0) {
            return $id;
        } else {
            throw new \RuntimeException("ID of record to update must be set (this means: not null, numeric and > 0). Id is '" . $id . "'");
        }
    }
}

