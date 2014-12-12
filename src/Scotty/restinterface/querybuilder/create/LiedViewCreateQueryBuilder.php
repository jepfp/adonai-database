<?php
namespace Scotty\restinterface\querybuilder\create;

/**
 * NOTICE:
 * This query builder is never called directly from the client as it only sends PUT (Update) requests.
 * LiedViewDAO converts the query if necessary from a PUT to a POST request.
 */
class LiedViewCreateQueryBuilder extends AbstractCreateQueryBuilder
{
    public function __construct($table, $request)
    {
        parent::__construct('fkliederbuchlied', $request);
    }
}

