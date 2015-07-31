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
    
    // TODO: A transaction would be needed here as well
    public static function countSourcePdfForLied($liedId)
    {
        $db = DatabaseConnector::db();
        $statement = $db->prepare("select count(*) as amount from filemetadata where filetype = 'sourcepdf' and lied_id = ?;");
        $statement->bind_param("i", $liedId);
        $statement->execute();
        $statement->bind_result($amount);
        
        $fetchResult = $statement->fetch();
        $statement->close();
        if ($fetchResult === true) {
            return $amount;
        } else {
            throw new \RuntimeException("Error while counting source pdf files for lied_id = " . $liedId);
        }
    }
}