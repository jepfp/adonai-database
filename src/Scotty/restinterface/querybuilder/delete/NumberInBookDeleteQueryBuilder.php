<?php
namespace Scotty\restinterface\querybuilder\delete;

class NumberInBookDeleteQueryBuilder extends AbstractDeleteQueryBuilder {
    public function __construct($table, $request)
    {
        parent::__construct('fkliederbuchlied', $request);
    }
}

