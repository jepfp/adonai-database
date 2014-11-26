<?php
namespace Scotty\song;

use \Scotty\database\DatabaseConnector;
use Scotty\database\DbHelper;
use \Scotty\changebacktrack\ChangeBacktrack;

class ChangeOrder
{

    private $logger;

    public function __construct()
    {
        $this->logger = \Logger::getLogger("main");
    }

    public function moveUp($table, $id)
    {
        $entries = $this->loadOtherTextsFromSameSong($table, $id);
        if ($this->isThisLiedtextAlreadyAtTop($entries, $id)) {
            return false;
        }
        $entries = $this->moveUpByIdInArray($entries, $id);
        $this->updateNewOrder($table, $entries);
        ChangeBacktrack::updateBacktrackOnLiedByChildTable($table, $id);
        return true;
    }

    private function loadOtherTextsFromSameSong($table, $textId)
    {
        $this->checkForInteger($textId);
        $db = DatabaseConnector::db();
        // hole alle mit dieser lied_id in ein Array
        $query = "SELECT id FROM " . $table . " WHERE lied_id = (SELECT lied_id FROM " . $table . " WHERE id = " . $textId . ") ORDER BY Reihenfolge";
        $resultset = $db->query($query);
        DbHelper::throwExceptionOnError($resultset, $db, $query);
        $entries = Array();
        while ($row = $resultset->fetch_row()) {
            $entries[] = $row[0];
        }
        $resultset->close();
        
        if (count($entries) < 1) {
            throw new \RuntimeException("Liedtext with id " . $textId . " does not exist.");
        }
        return $entries;
    }

    private function checkForInteger($id)
    {
        if (! is_numeric($id)) {
            throw new \Exception("Invalid id. The given id (" . $id . ") is not an integer.");
        }
    }

    private function isThisLiedtextAlreadyAtTop($entries, $id)
    {
        return $entries[0] == $id;
    }

    private function moveUpByIdInArray($entries, $id)
    {
        $this->logger->debug("Reihenfolge b4 move up of $id: " . implode(", ", $entries));
        for ($i = 1; $i < count($entries); $i ++) {
            if ($entries[$i] == $id) {
                $entryBefore = $entries[$i - 1];
                $entries[$i - 1] = $id;
                $entries[$i] = $entryBefore;
                break;
            }
        }
        $this->logger->debug("Reihenfolge after move up of $id: " . implode(", ", $entries));
        return $entries;
    }

    private function updateNewOrder($table, $entries)
    {
        $statement = "";
        for ($i = 1; $i <= count($entries); $i ++) {
            $newStatement = "UPDATE $table SET Reihenfolge = '$i' where id = '" . $entries[$i - 1] . "';";
            ChangeBacktrack::logStatement($table, $newStatement, null);
            $statement .= $newStatement;
        }
        $this->logger->debug("Going to execute the following statements: " . $statement);
        $db = DatabaseConnector::db();
        $result = $db->multi_query($statement);
        //flush multi_queries (http://php.net/manual/de/mysqli.multi-query.php#113840)
        while ($db->next_result())
        {
            if (!$db->more_results()) break;
        }
        DbHelper::throwExceptionOnError($result, $db, $statement);
    }
    
    public function moveDown($table, $id)
    {
        $entries = $this->loadOtherTextsFromSameSong($table, $id);
        if ($this->isThisLiedtextAlreadyAtBottom($entries, $id)) {
            return false;
        }
        $entries = $this->moveDownByIdInArray($entries, $id);
        $this->updateNewOrder($table, $entries);
        ChangeBacktrack::updateBacktrackOnLiedByChildTable($table, $id);
        return true;
    }
    
    private function isThisLiedtextAlreadyAtBottom($entries, $id)
    {
        return $entries[count($entries) - 1] == $id;
    }
    
    private function moveDownByIdInArray($entries, $id)
    {
        $this->logger->debug("Reihenfolge b4 move down of $id: " . implode(", ", $entries));
        for ($i = 0; $i < (count($entries) - 1); $i ++) {
            if ($entries[$i] == $id) {
                $entryAfter = $entries[$i + 1];
                $entries[$i + 1] = $id;
                $entries[$i] = $entryAfter;
                break;
            }
        }
        $this->logger->debug("Reihenfolge after move down of $id: " . implode(", ", $entries));
        return $entries;
    }
}