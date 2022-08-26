<?php
namespace Scotty\changebacktrack;

use Scotty\session\SessionInfoProvider;
use Scotty\database\DatabaseConnector;
use Scotty\database\DbHelper;
use Scotty\auth\UserDTO;

class ChangeBacktrack
{
    public static function logStatement($table, $query, $params)
    {
        if ($params != null) {
            $params = implode(", ", $params);
        }
        $dbLogger = \Logger::getLogger("dbLogger");
        $sessionInfoProvider = new SessionInfoProvider();
        $userDto = $sessionInfoProvider->getCurrentUserDTO();
        $text = $userDto->id . " ## " . $userDto->email . " ## " . $table . " ## " . $query . " ## " . $params;
        $dbLogger->info($text);
    }
    
    // TODO: Put a transaction around!
    public static function updateBacktrackOnLiedByChildTable($table, $id)
    {
        $logger = \Logger::getLogger("main");

        $sessionInfoProvider = new SessionInfoProvider();
        $userDto = $sessionInfoProvider->getCurrentUserDTO();
        $statement = "UPDATE lied SET updated_at = NOW(), lastEditUser_id = " . $userDto->id;
        $statement .= " WHERE id = (SELECT lied_id FROM " . $table . " WHERE id = " . $id . ");";
        $logger->trace("Built query for ChangeBacktrack: " . $statement);
        $db = DatabaseConnector::db();
        $result = $db->query($statement);
        DbHelper::throwExceptionOnError($result, $db, $statement);
    }
}