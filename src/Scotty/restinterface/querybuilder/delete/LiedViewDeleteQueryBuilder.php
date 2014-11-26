<?php
namespace Scotty\restinterface\querybuilder\delete;

class LiedViewDeleteQueryBuilder extends LiedDeleteQueryBuilder
{
    public function __construct($table, $request)
    {
        parent::__construct("lied", $request);
        $this->logger->info("Delegating deletion of liedview to lied.");
    }
}

