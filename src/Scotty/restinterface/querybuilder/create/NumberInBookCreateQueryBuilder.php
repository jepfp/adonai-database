<?php
namespace Scotty\restinterface\querybuilder\create;

class NumberInBookCreateQueryBuilder extends AbstractCreateQueryBuilder
{

    public function __construct($table, $request)
    {
        parent::__construct('fkliederbuchlied', $request);
    }
}

