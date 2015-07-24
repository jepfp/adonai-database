<?php
namespace Scotty\file;

use \Scotty\database\DatabaseConnector;
use Scotty\database\DbHelper;

class FileMetadataHelper
{
    
    // TODO: A transaction would be needed, because FILE and FILEMETADATA entry should exist together!
    public static function createSourcePdfFileMetadataEntry($liedId)
    {
        $db = DatabaseConnector::db();
        $query = "INSERT INTO filemetadata (filetype, lied_id) VALUES ('sourcepdf', ?)";
        $statement = $db->prepare($query);
        $statement->bind_param("i", $liedId);
        $queryResult = $statement->execute();
        $insertedId = $db->insert_id;
        DbHelper::throwExceptionOnStatementError($statement);
        $affectedRows = $statement->affected_rows;
        $statement->close();
        if ($affectedRows != 1 || $insertedId < 1) {
            throw new \RuntimeException("Adding entry to filemetadata failed. lied_id=" . $liedId);
        }
        return $insertedId;
    }
}