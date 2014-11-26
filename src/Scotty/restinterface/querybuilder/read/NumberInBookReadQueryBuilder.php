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
        $this->bindLiedId();
        $query = "select fk.id as id, l.id as liederbuch_id, l.Buchname, l.Beschreibung, l.mnemonic, fk.Liednr, l.locked, fk.lied_id from liederbuch l left join (select * from fkliederbuchlied l where l.lied_id = ?) fk ON (l.id = fk.liederbuch_id);";
        $statement = $db->prepare($query);
        DbHelper::throwExceptionOnError($statement, $db, $query);
        DbHelper::bindParams($statement, $this->bindParam);
        $this->logStatement($query, $this->bindParam->get());
        return $statement;
    }

    private function bindLiedId()
    {
        $whereParam = $this->request->getRequestParamAsDecodedJson("filter");
        foreach ($whereParam as $aParam) {
            $property = self::removeNamespaceFromFilterProperty($aParam->property);
            if ($property == "lied_id") {
                $this->bindParam->add("i", $aParam->value);
                return;
            }
        }
        
        // no lied_id filter set?
        throw new DatabaseException("No lied_id filter was set for " + getClass());
    }
}

