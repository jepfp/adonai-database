<?php
namespace Scotty\restinterface\querybuilder\update;

class NumberInBookUpdateQueryBuilder extends AbstractUpdateQueryBuilder
{

    public function __construct($table, $request)
    {
        parent::__construct('fkliederbuchlied', $request);
    }
}

