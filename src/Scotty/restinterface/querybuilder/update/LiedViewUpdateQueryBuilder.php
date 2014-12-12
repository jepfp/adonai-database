<?php
namespace Scotty\restinterface\querybuilder\update;

class LiedViewUpdateQueryBuilder extends AbstractUpdateQueryBuilder
{

    public function __construct($table, $request)
    {
        parent::__construct('fkliederbuchlied', $request);
    }

    protected function buildWhereQueryString()
    {
        return "WHERE lied_id = ? and liederbuch_id = ?";
    }

    protected function buildWhereValues()
    {
        $liedId = $this->dto->findFieldByKey("lied_id")->getValue();
        $liederbuchId = $this->dto->findFieldByKey("liederbuch_id")->getValue();
        $this->bindParam->add("s", $liedId);
        $this->bindParam->add("s", $liederbuchId);
    }
}

