<?php
namespace Scotty\restinterface\querybuilder\read;

use Scotty\database\DatabaseException;
use \Scotty\restinterface\querybuilder\BindParam;
use Scotty\database\DbHelper;

class NumberInBookReadQueryBuilder extends AbstractReadQueryBuilder
{

    public function build($db)
    {
        $this->bindParam = new BindParam();
        
        if ($this->request->id != null) {
            $this->bindParam->add("i", $this->request->id);
            $liedIdColumn = "fk.lied_id";
            $joinString = "join";
            $whereColumn = "l.id";
        } else {
            $this->bindLiedId();
            $this->bindLiedId(); // 2 times because the param is needed twice
            $liedIdColumn = "? as lied_id";
            $joinString = "left join";
            $whereColumn = "l.lied_id";
        }
        $query = "select fk.id as id, l.id as liederbuch_id, l.Buchname, l.Beschreibung, l.mnemonic, fk.Liednr, l.locked, $liedIdColumn from liederbuch l $joinString (select * from fkliederbuchlied l where $whereColumn = ?) fk ON (l.id = fk.liederbuch_id);";
        $statement = $db->prepare($query);
        DbHelper::throwExceptionOnError($statement, $db, $query);
        DbHelper::bindParams($statement, $this->bindParam);
        $this->logStatement($query, $this->bindParam->get());
        return $statement;
    }

    private function bindLiedId()
    {
        $whereParam = $this->request->getRequestParamAsDecodedJson("filter");
        if (! $whereParam) {
            $this->throwRequirementsNotMet();
        }
        foreach ($whereParam as $aParam) {
            $property = self::removeNamespaceFromFilterProperty($aParam->property);
            if ($property == "lied_id") {
                $value = null;
                if(isset($aParam->value)){
                    $value = $aParam->value;
                }
                $this->bindParam->add("i", $value);
                return;
            }
        }
        
        $this->throwRequirementsNotMet();
    }

    private function throwRequirementsNotMet()
    {
        throw new DatabaseException("Either a lied.lied_id filter or fkLiederbuchLied.id must be set for " . get_class());
    }
}

