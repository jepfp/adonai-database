<?php
namespace Scotty\database;

class DatabaseException extends \RuntimeException
{
    
    public static function constructWithDbErrorAndQuery($dbErrorMessage, $query){
        $message = "The following builder statement is invalid.";
        $message .= "MySQL last error message: " . $dbErrorMessage . "\n";
        $message .= "Query: " . $query;
        return new DatabaseException($message);
    }
    
    public static function constructFromStatement($statement){
        $message = "The following error occured while executing an sql statement: " . $statement->error;
        return new DatabaseException($message);
    }

    public function __construct($message){
        parent::__construct($message);
    }
}