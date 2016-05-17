<?php
namespace Scotty\restinterface\querybuilder\update;

use \Scotty\restinterface\querybuilder\BindParam;
use Scotty\restinterface\querybuilder\AbstractModificationQueryBuilder;

class AbstractUpdateQueryBuilder extends AbstractModificationQueryBuilder
{

    protected function buildQueryString()
    {
        $fields = $this->dto->getAllFieldNamesInParamsAndFieldsSetByApplication();
        return "UPDATE " . $this->table . " SET " . implode(" = ?, ", $fields) . "= ? " . $this->buildWhereQueryString();
    }

    protected function buildWhereQueryString()
    {
        return "WHERE id = ?";
    }

    protected function buildValues()
    {
        // Only get the key value pairs which were sent in the request or added to the list fieldsSetByApplication.
        $keyValuePairs = $this->dto->getAllKeyValuePairsInParamsAndFieldsSetByApplication();
        foreach ($keyValuePairs as $key => $value) {
            $this->bindParam->add("s", $value);
        }
        $this->buildWhereValues();
    }

    protected function buildWhereValues()
    {
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

