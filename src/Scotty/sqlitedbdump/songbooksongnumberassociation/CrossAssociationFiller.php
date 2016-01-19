<?php
namespace Scotty\sqlitedbdump\songbooksongnumberassociation;

use Scotty\database\DbHelper;
use Scotty\restinterface\querybuilder\AssocBinder;

/**
 * Makes the songnumbers of each songbook available to all other songbooks by adding
 * a songnumber to the other books with the prefix of the own songbook.
 * example:
 * lied with id 4
 * liederbuch with id 2 and mnemonic LU
 * liederbuch with id 3 and mnemonic BE
 * fkliederbuchlied with Liednr 6, lied_id 4 and liederbuch_id 2
 * --> A new entry in fkliederbuchlied is added with LiedNr LU6, lied_id 4 and liederbuch_id 3
 */
class CrossAssociationFiller
{

    private $logger;

    private $db;

    private $sqliteDb;

    private $songbooks;

    public function __construct()
    {
        $this->logger = \Logger::getLogger("main");
    }

    public function fill($db, $sqliteDb)
    {
        $this->db = $db;
        $this->sqliteDb = $sqliteDb;
        $this->loadAllSongbooks();
        $this->complementAssociations();
    }

    private function loadAllSongbooks()
    {
        $query = "SELECT * FROM liederbuch";
        $this->songbooks = $this->executeSelect($query);
        $this->logger->trace("Loaded all songbooks. Amount: " . count($this->songbooks));
    }

    private function executeSelect($query)
    {
        $statement = $this->db->prepare($query);
        DbHelper::throwExceptionOnError($statement, $this->db, $query);
        $statement->execute();
        $statement->store_result();
        $entries = $this->fetchAndTransformResult($statement);
        return $entries;
    }

    //try to maybe refactor to an extern class
    private function fetchAndTransformResult($statement)
    {
        $row = array();
        AssocBinder::bind($statement, $row);
        $transformedResult = array();
        while ($statement->fetch()) {
            foreach ($row as $key => $value) {
                $row_copy[$key] = $value;
            }
            $transformedResult[] = $row_copy;
        }
        return $transformedResult;
    }

    private function complementAssociations()
    {
        foreach ($this->songbooks as $aSongbook) {
            $this->logger->trace("Inserting numbers for book '" . $aSongbook['Buchname'] . "' into other books.");
            $allNumberEntriesForThisBook = $this->loadAllNumberEntriesForSongbook($aSongbook['id']);
            $this->insertNumberEntriesIntoAllOtherSongbooks($aSongbook, $allNumberEntriesForThisBook);
        }
    }

    private function loadAllNumberEntriesForSongbook($songbookId)
    {
        $query = "SELECT * FROM fkliederbuchlied where liederbuch_id = " . $songbookId;
        return $this->executeSelect($query);
    }

    private function insertNumberEntriesIntoAllOtherSongbooks($currentSongbook, $numberEntriesToInsert)
    {
        foreach ($this->songbooks as $aSongbook) {
            if ($aSongbook['id'] == $currentSongbook['id']) {
                // don't add the already existing association again
                continue;
            }
            $this->logger->trace("-Inserting numbers into '" . $aSongbook['Buchname'] . "'");
            $this->insertNumberEntriesIntoSongbook($aSongbook, $currentSongbook['mnemonic'], $numberEntriesToInsert);
        }
    }

    private function insertNumberEntriesIntoSongbook($songbookToInsert, $mnemonic, $numberEntriesToInsert)
    {
        foreach ($numberEntriesToInsert as $aNumber) {
            $this->insertAssociation($songbookToInsert['id'], $aNumber['lied_id'], $mnemonic, $aNumber['Liednr']);
        }
    }

    private function insertAssociation($liederbuchId, $liedId, $mnemonic, $liedNr)
    {
        $insertQuery = "INSERT INTO fkliederbuchlied (liederbuch_id, lied_id, Liednr) VALUES ($liederbuchId, $liedId, '$mnemonic$liedNr')";
        $querySuccessful = $this->sqliteDb->exec($insertQuery);
        $this->logger->trace("--Inserted '$mnemonic$liedNr' for lied_id " . $liedId);
        if (! $querySuccessful || $this->sqliteDb->lastErrorCode() !== 0) {
            $this->logger->error("Error while inserting a cross association into sqlite database (skipping this entry/insert!): " . $this->sqliteDb->lastErrorMsg() . "\nQuery is: " . $insertQuery);
        }
    }
}

?>