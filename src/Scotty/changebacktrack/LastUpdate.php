<?php
namespace Scotty\changebacktrack;

use Scotty\session\SessionInfoProvider;
use Scotty\database\DatabaseConnector;
use Scotty\database\DbHelper;
use Scotty\auth\UserDTO;

class LastUpdate
{

    public static function determineUpdatedAtOfLatestUpdatedLied()
    {
        $db = DatabaseConnector::db();
        $query = "SELECT id, Titel, updated_at, lastEditUser_id from lied ORDER BY updated_at DESC LIMIT 1";
        $statement = $db->prepare($query);
        DbHelper::throwExceptionOnError($statement, $db, $query);
        $statement->execute();
        $statement->bind_result($id, $titel, $updatedAt, $lastEditUserId);
        $statement->fetch();
        $logger = \Logger::getLogger("main");
        $logger->info("LastUpdate::determineUpdatedAtOfLatestUpdatedLied() called. Newest lied is '$titel' (id: $id, edited by user id: $lastEditUserId, updated at: $updatedAt)");
        return $updatedAt;
    }
}